<?php
declare(strict_types=1);
error_reporting(-1);

require_once('TCPDF-6.2.17/tcpdf.php');
require_once('TCPDF-6.2.17/tcpdf_barcodes_2d.php');
require_once('TCPDF-6.2.17/tcpdf_barcodes_1d.php');
require_once('mail.php');
require_once('_YAML/Spyc.php');

function fail_on_err_handler($err_num, $err_str, $err_file, $err_line) {
	throw new ErrorException("$err_num $err_str $err_file:$err_line");
}

function fail_on_error() {
	set_error_handler('fail_on_err_handler');
}

fail_on_error();

function state_init() {
	$rc = session_start();
	assert($rc == TRUE);
}

function state_trans_from_to(string $from = NULL, string $to) {
	if ($from != NULL) {
		if ($_SESSION['state'] != $from) {
			header("Location: /error.html");
		}
	}
	$_SESSION['state'] = $to;
}

function load_json_file(string $fn) : array {
	$fp = fopen($fn, 'r');
	assert($fp != FALSE);

	$fsz = fstat($fp)['size'];
	assert($fsz >= 0);

	$data_read = fread($fp, $fsz);
	assert(strlen($data_read) == $fsz);

	$rc = fclose($fp);
	assert($rc != FALSE);

	$obj = json_decode($data_read);
	assert($obj != NULL);

	$arr = get_object_vars($obj);
	return $arr;
}

function load_yaml_file(string $fn) : array {
	$array = Spyc::YAMLLoad($fn);
	return $array;
}

function get_config() {
	return load_json_file("config.json");
}


// From OWASP page
function xssafe($data,$encoding='UTF-8') {
	return htmlspecialchars($data,ENT_QUOTES | ENT_HTML401,$encoding);
}

function trim_to_len(string $s, int $len) : string {
	return substr($s, 0, $len);
}

function post_make_sane(array $post_req) : array {
	$max_field_len = 256;
	$max_num_of_fields = 50;
	$arr = array();

	$field_num = 0;
	foreach ($post_req as $raw_key => $raw_value) {
		$key = xssafe(trim_to_len($raw_key, $max_field_len));
		$value = xssafe(trim_to_len($raw_value, $max_field_len));

		$arr[$key] = $value;
		$field_num += 1;

		assert($field_num < $max_num_of_fields);
	}

	return $arr;
}

function get_order_from_post(array $tained_post_data) {
	return post_make_sane($tained_post_data);
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

function tm_str_usd_amt(float $val) : string {
	return sprintf("$%2.2f", $val);
}

function tm_str_new_mem_ini_fee() : string {
	return "Toastmasters International: new member processing fee";
}

function tm_str_monthly(int $how_many_months, float $how_much_per_month, string $name = "Toastmasters International") : string {
	assert($how_many_months >= 1 && $how_many_months <= 12);
	return "$name: $how_many_months [months] x " . tm_str_usd_amt($how_much_per_month);
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

// mo	which_mo	pro-rated in some months
// jan	0		3
// feb	1		2
// mar	2		1
// apr	3		6
// may	4		5
// june	5		4
// july	6		3
// aug	7		2
// sep	8		1
// oct	9		6
// nov	10		5
// dec	11		4

function make_order_items_array(array $cfg, array $order) : array {
	$order_memb_type = $order['membership_type'];
	$midx = month_name_to_idx($order['mptm_start_month']);
	$how_many_months = [3,2,1,6,5,4,3,2,1,6,5,4][$midx];

	$club_cfg = $cfg['tm']->{'clubs'}[0];

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
	$amt = $how_many_months * $tmi_mo_cost;
	array_push($item_all, [ $item_num, tm_str_monthly($how_many_months, $tmi_mo_cost), $amt ]);
	$item_num += 1;

	// c. we charge club's per-month fee
	$club_mo_cost = $club_cfg->{'fees'}->{'club_monthly'};
	$amt = $how_many_months * $club_mo_cost;
	array_push($item_all, [ $item_num, tm_str_monthly($how_many_months, $club_mo_cost, "Menlo Park Toastmasters"), $amt ]);
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

function club_get_by_name($cfg, string $name) {
	$clubs_all = $cfg['tm']->{'clubs'};
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

	$pdf->Output('/tmp/receipt.pdf', 'FI');
}

function html_report_make($cfg, array $order_items, $order) : string {
	$total_raw = $order_items[count($order_items) - 1][2];
	$total = sprintf("%2.2f", $total_raw);
	$link = "https://paypal.me/mptm/$total";
	$table = make_full_table($order_items);

	$tbl = "";
	$tbl .= "<html><body>";

	$tbl .= "<h1>Menlo Park Toastmasters: Membership!</h1>";

	$tbl .= "<p>";
	$tbl .= "Menlo Park Toastmasters is a chapter of a bigger non-profit organization ";
	$tbl .= "called Toastmasters International (TMI). TMI provides us educational ";
	$tbl .= "materials and a structured program for out meetings, while Menlo Park ";
	$tbl .= "Toastmasters conducts the meetings and sticks to TMI protocol. ";
	$tbl .= "</p>";

	$tbl .= "<h2>Pay online:</h2>";
	$tbl .= "<p>";
	$tbl .= "You can pay with Debit/Credit Card or PayPal here:";
	$tbl .= "</p>";
	$tbl .= '<a href="'.$link.'"><h2>Click HERE to pay $' . $total . '</h2></a>';

	// @todo: replace with TCPDF barcode so that working offline is possible

//	$tbl .= "<h2>QR code</h2>";
	$tbl .= "<p>To pay from iPhone, scan this image with iPhone camera:</p>";
//	$tbl .= '<img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.$link.'&choe=UTF-8" />';

	$tbl .= "<p></p>";

	$tbl .= "<h3>Explanation of fees:</h3>";

	$tbl .= $table;

	$tbl .= "<p>";
	$tbl .= "This receipt should have been e-mailed to you in text and the PDF format";
	$tbl .= "</p>";
	$tbl .= "<p>";
	$tbl .= "This receipt is only valid when presented with a proof of payment.";
	$tbl .= "</p>";


	$tbl .= "<h2>Questions?</h2>";
	$tbl .= "<p>";
	$tbl .= 'E-mail <a href="mailto:treasurer@menloparktm.org">treasurer@menloparktm.org</a> in case you have any questions.';
	$tbl .= "</p>";



	$tbl .= "<h2>You've applied with data</h2>";
	$tbl .= "<pre>";
	$tbl .= print_r($order, TRUE);
	$tbl .= "</pre>";


	$tbl .= "</body>";
	$tbl .= "</html>";
	return $tbl;
}

function version_num_verify(string $num_str) : int {
	$num = (int)$num_str;
	assert($num >= 0 && $num <= 99);
	return $num;
}

function php_version_int() : int {
	$ver_str = phpversion();
	$chunks = explode('.', $ver_str);

	assert(count($chunks) == 3);

	$vmaj   = version_num_verify($chunks[0]);
	$vmin   = version_num_verify($chunks[1]);
	$vpatch = version_num_verify($chunks[2]);

	$vint =	($vmaj * pow(10,4)) + ($vmin * pow(10,2)) + $vpatch;

	return $vint;
}

function email_with_form_and_pdf(array $raw_post) {
	$order = get_order_from_post($raw_post);
	assert($order != NULL);

	$cfg = get_config();
	assert($cfg != NULL);

	$order_items = make_order_items_array($cfg, $order);
	assert($order_items != NULL);

	// @todo: tools for php static analysis
	// @todo: php switch for strict code checking

	$html_rep = html_report_make($cfg, $order_items, $order);
	pdf_generate($html_rep);

	$mail_cfg = load_json_file('mail_conf.js');
	assert($mail_cfg != NULL);
	$mail = email_make($mail_cfg, $order, $html_rep, "PDF attached");

	return $mail;
}

?>
