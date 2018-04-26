<?php
declare(strict_types=1);
error_reporting(-1);

require_once('TCPDF-6.2.17/tcpdf.php');
require_once('TCPDF-6.2.17/tcpdf_barcodes_2d.php');
require_once('TCPDF-6.2.17/tcpdf_barcodes_1d.php');

function fail_on_err_handler($err_num, $err_str, $err_file, $err_line) {
	throw new ErrorException("$err_num $err_str $err_file:$err_line");
}

function fail_on_error() {
	set_error_handler('fail_on_err_handler');
}

fail_on_error();

function gcfg_max_size() : int	{ return 1024*16; }

function out_dir_name() : string {
	$here = getcwd();
	$base = "newmem";

	$dn = $here . "/" . $base;

	return $dn;
}

function has_enough_disk_space() : bool {
	$dn = out_dir_name();
	$mb = 1024.0*1024.0;
	$fsz_mb = disk_free_space($dn) / $mb;
	return ($fsz_mb > 1.0);
}

function pre_check_all() : array {
	$dn = out_dir_name();
	if (!file_exists($dn) || !is_dir($dn)) {
		return[-__LINE__, "The server isn't properly setup to accept your application"];
	}
	if (stat($dn)['mode'] == 40777) {
		return[-__LINE__, "The server's directory isn't properly configured"];
	}
	if (!has_enough_disk_space()) {
		return [-__LINE__, "No space on disk to save your submission"];
	}
	return [0, "ok"];
}

function make_random_fn() : string {
	$random_str = "";
	for ($i = 0; $i < 4; $i++) {
		$ri = random_int (0x0, 0xffffffff);
		$random_str .= "x" . $ri;
	}
	return sha1($random_str);
}

function make_out_fn() : string {
	return "newmem." . make_random_fn();
}

function state_init() {
	$rc = session_start();
	assert($rc == TRUE);
}

function state_trans_from_to(string $from = null, string $to) {
	if ($from != null) {
		if ($_SESSION['state'] != $from) {
			header("Location: /error.html");
		}
	}
	$_SESSION['state'] = $to;
}


// @todo: enable warnings everywhere

function load_json_file(string $fn) {
	$fp = fopen($fn, 'r');
	assert($fp != FALSE);

	$fsz = fstat($fp)['size'];
	assert($fsz >= 0);

	$data_read = fread($fp, $fsz);
	assert(strlen($data_read) == $fsz);

	$rc = fclose($fp);
	assert($rc != FALSE);

	$ret_obj = json_decode($data_read);
	assert($ret_obj != NULL);

	return $ret_obj;
}

function json_file_save($obj, string $fn_out, int $max_size) : bool {
	$j_str = json_encode($obj, JSON_PRETTY_PRINT);
	assert($j_str != FALSE);

	if ($max_size != -1) {
		if (strlen($j_str) > $max_size) {
			return FALSE;
		}
	}

	$fp = fopen($fn_out, 'w');
	assert($fp != FALSE);

	$rc = fwrite($fp, $j_str);
	assert($rc != FALSE);

	$rc = fclose($fp);
	assert($rc != FALSE);

	return TRUE;
}


function get_config() {
	return load_json_file("config.json");
}

function get_order(string $fn_in) {
	return load_json_file($fn_in);
}

function make_hdr(string $num, string $name, string $val) : string {
	$ostr = "";
	$ostr .= "  <tr bgcolor=\"lightgray\">\n";
	$ostr .= "    <th width=\"5%\">$num</th>\n";
	$ostr .= "    <th width=\"60%\">$name</th>\n";
	$ostr .= "    <th width=\"25%\" align=\"right\"><tt>$val</tt></th>\n";
	$ostr .= "  </tr>\n";
	return $ostr;
}

function row_add(string $num, string $name, string $val) : string {
	$val_padded = sprintf("$%2.2f", $val);
	$ostr = "";
	$ostr .= "  <tr>\n";
	$ostr .= "    <td>$num</td>\n";
	$ostr .= "    <td>$name</td>\n";
	$ostr .= "    <td align=\"right\"><tt>$val_padded</tt></td>\n";
	$ostr .= "  </tr>\n";
	return $ostr;
}

// @todo: usb -> usd
function tm_str_usb_amt(float $val) : string {
	return sprintf("$%2.2f", $val);
}

function tm_str_new_mem_ini_fee() : string {
	return "Toastmasters International: new member processing fee";
}

function tm_str_monthly(int $how_many_months, float $how_much_per_month, string $name = "Toastmasters International") : string {
	assert($how_many_months >= 1 && $how_many_months <= 12);
	return "$name: $how_many_months * " . tm_str_usb_amt($how_much_per_month);
}

function assert_rate(float $rate) {
	assert($rate >= 0.00 && $rate <= 1.00);
}

function tm_str_ca_tax(float $rate) : string {
	assert_rate($rate);
	$pc_rate = $rate * 100;
	$pc_rate .= "%";
	return "CA sales tax (San Mateo) of $pc_rate";
}

function tm_str_paypal(float $rate) : string {
	assert_rate($rate);
	$pc_rate = $rate * 100;
	$pc_rate .= "%";
	return "PayPal payment processing rate of $pc_rate";
}

function tm_str_total() : string {
	return "Total";
}

function month_name_to_idx(string $name) : int {
	$month_names = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
	$ret = array_search($name, $month_names);
	assert($ret != FALSE);
	return (int)$ret;
}

function make_order_items_array($cfg, $order) : array {
	assert($cfg != NULL);
	assert($order != NULL);

	$order_memb_type = $order->{'post'}->{'membership_type'};
	$order_memb_start_mo = 1 + month_name_to_idx($order->{'post'}->{'mptm_start_month'});

	$club_cfg = $cfg->{'tm'}->{'clubs'}[0];

	$item_all = [];
	$item_num = 1;

	// a. For new members, we need to charge the initiation fee
	if ($order_memb_type == "new") {
		//echo ">>> new membership\n";
		$amt = $club_cfg->{'fees'}->{'init_onetime'};
		array_push($item_all, [ $item_num, tm_str_new_mem_ini_fee(), $amt ]);
		$item_num += 1;
	}

	// b. we charge TMI's per-month fee
	$tmi_mo_cost = $club_cfg->{'fees'}->{'tmi_monthly'};
	$amt = $order_memb_start_mo * $tmi_mo_cost;
	array_push($item_all, [ $item_num, tm_str_monthly($order_memb_start_mo, $tmi_mo_cost), $amt ]);
	$item_num += 1;

	// c. we charge club's per-month fee
	$club_mo_cost = $club_cfg->{'fees'}->{'club_monthly'};
	$amt = $order_memb_start_mo * $club_mo_cost;
	array_push($item_all, [ $item_num, tm_str_monthly($order_memb_start_mo, $club_mo_cost, "Menlo Park Toastmasters"), $amt ]);
	$item_num += 1;

	// add itemized charges here

	// x. charge CA tax
	$ca_tax_rate = $club_cfg->{'fees'}->{'ca_tax_rate_mul'};
	$total_so_far = 0;
	foreach ($item_all as $arr_idx => $arr) {
		$total_so_far += $arr[2];
	}
	$amt = $total_so_far * ($ca_tax_rate);
	array_push($item_all, [ $item_num, tm_str_ca_tax($ca_tax_rate), $amt ]);
	$item_num += 1;

	// y. charge PayPal rate
	$paypal_rate = $club_cfg->{'fees'}->{'paypal_rate_mul'};
	$total_so_far = 0;
	foreach ($item_all as $arr_idx => $arr) {
		$total_so_far += $arr[2];
	}
	$amt = $total_so_far * ($paypal_rate);
	array_push($item_all, [ $item_num, tm_str_paypal($paypal_rate), $amt ]);
	$item_num += 1;

	// z. add a total count
	$total_amt = 0;
	foreach ($item_all as $arr_idx => $arr) {
		$total_amt += $arr[2];	// amount
	}
	array_push($item_all, [ '<b>=</b>', '<b>' . tm_str_total() . '</b>', $total_amt ]);
	$item_num += 1;

	return $item_all;
}

function make_full_table($order_items) : string {
	// @todo: null -> NULL in source code

	$tbl = "<table>\n";
	$tbl .= make_hdr("#", "Name", "Amount");

	foreach ($order_items as $arr_idx => $arr_row) {
		$tbl .= row_add(
				sprintf("%s",$arr_row[0]),
				$arr_row[1],
				sprintf("%2.2f", $arr_row[2])
			);
	}

	$tbl .= "</table>\n";
	assert($tbl != NULL);

	return $tbl;
}

function club_get_by_name($cfg, string $name) : ?stdClass {
	$clubs_all = $cfg->{'tm'}->{'clubs'};
	foreach ($clubs_all as $k => $v) {
		if (strcmp($v->{'alias'}, $name) == 0) {
			return $v;
		}
	}
	return NULL;
}

function pdf_generate(string $html) {
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator("");
	$pdf->SetAuthor('Menlo Park Toastmasters');
	$pdf->SetTitle('Menlo Park Toastmasters, receipt');
	$pdf->SetSubject('Menlo Park Toastmasters, receipt');
	$pdf->SetKeywords('Menlo Park, Toastmasters, receipt, bill');

	// set default header data
	//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 048', PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// set font
	$pdf->SetFont('helvetica', '', 12);

	// add a page
	$pdf->AddPage();

	// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
	$pdf->writeHTML($html, false, false, false, false, 'L');

	$pdf->Output('receipt.pdf', 'I');
}

function pdf_report_make($cfg, array $order_items) : string {
	$total_raw = $order_items[count($order_items) - 1][2];
	$total = sprintf("%2.2f", $total_raw);
	$link = "https://paypal.me/mptm/$total";
	$table = make_full_table($order_items);

	$tbl = "";
	$tbl .= "<html><body>";

	$tbl .= "<h1>Menlo Park Toastmasters: Membership!</h1>";

	$tbl .= "<p>";
	$tbl .= "Menlo Park Toastmasters is a chapter of a bigger non-profit organization";
	$tbl .= "called Toastmasters International (TMI). TMI provides us educational";
	$tbl .= "materials and a structured program for out meetings, while Menlo Park";
	$tbl .= "Toastmasters conducts the meetings and sticks to TMI protocol.";
	$tbl .= "</p>";

	$tbl .= "<h2>Pay online:</h2>";
	$tbl .= "<p>";
	$tbl .= "You can pay with Debit/Credit Card or PayPal here:";
	$tbl .= "</p>";
	$tbl .= '<a href="'.$link.'"><h2>Click HERE to pay $' . $total . '</h2></a>';

//	$tbl .= "<h2>QR code</h2>";
	$tbl .= "<p>To pay from iPhone, scan this image with iPhone camera:</p>";
	$tbl .= '<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$link.'&choe=UTF-8" />';

	$tbl .= "<p></p>";

	$tbl .= "<h3>Explanation of fees:</h3>";

	$tbl .= $table;

	$tbl .= "<p>";
	$tbl .= "This receipt is only valid when presented with a proof of payment.";
	$tbl .= "</p>";


	$tbl .= "<h2>Questions?</h2>";
	$tbl .= "<p>";
	$tbl .= 'E-mail <a href="mailto:treasurer@menloparktm.org">treasurer@menloparktm.org</a> in case you have any questions.';
	$tbl .= "</p>";

	$tbl .= "</body>";
	$tbl .= "</html>";
	return $tbl;
}

?>
