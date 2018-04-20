<?php

// @todo: enable warnings everywhere

function load_json_file($fn) {
	$fp = fopen($fn, 'r');
	assert($fp != FALSE);
	$fsz = fstat($fp)['size'];
	$data_read = fread($fp, $fsz);
	fclose($fp);
	assert(strlen($data_read) == $fsz);
	$ret_str = json_decode($data_read);
	assert($ret_str != NULL);
	return $ret_str;
}

function get_config() {
	return load_json_file("config.json");
}

function get_order() {
	return load_json_file('data.txt');
}

function make_hdr($num, $name, $val) {
	$ostr = "";
	$ostr .= "  <tr bgcolor=\"lightgray\">\n";
	$ostr .= "    <th width=\"5%\">$num</th>\n";
	$ostr .= "    <th width=\"60%\">$name</th>\n";
	$ostr .= "    <th width=\"25%\" align=\"right\"><tt>$val</tt></th>\n";
	$ostr .= "  </tr>\n";
	return $ostr;
}

function row_add($num, $name, $val) {
	$val_padded = sprintf("%2.2f", $val);
	$ostr = "";
	$ostr .= "  <tr>\n";
	$ostr .= "    <td>$num</td>\n";
	$ostr .= "    <td>$name</td>\n";
	$ostr .= "    <td align=\"right\"><tt>$val_padded</tt></td>\n";
	$ostr .= "  </tr>\n";
	return $ostr;
}

function tm_str_usb_amt($val) {
	// @todo: assert float
	return sprintf("$%2.2f", $val);
}

function tm_str_new_mem_ini_fee() {
	return "Toastmasters International: new member processing fee";
}

function tm_str_monthly($how_many_months, $how_much_per_month) {
	// @todo: assert ints
	return "Toastmasters International: $how_many_months * " . tm_str_usb_amt($how_much_per_month);
}

function tm_str_ca_tax($rate) {
	// @todo: assert int
	$pc_rate = $rate * 100;
	$pc_rate .= "%";
	return "CA Tax of $pc_rate";
}

function tm_str_paypal($rate) {
	// @todo: assert float
	// @todo: find a library for secure type conversion in PHP or check if types are strictly enforced in PHP7
	$pc_rate = $rate * 100;
	$pc_rate .= "%";
	return "PayPal payment processing rate of $pc_rate";
}

function tm_str_total() {
	return "Total";
}

function make_order_items_array($cfg, $order) {
	assert($cfg != NULL);
	assert($order != NULL);

	$order_memb_type = $order->{'post'}->{'membership_type'};
	$order_memb_start_mo = $order->{'post'}->{'tm_start_month'};
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
	array_push($item_all, [ $item_num, tm_str_monthly($order_memb_start_mo, $club_mo_cost), $amt ]);
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
	$paypal_rate = $club_cfg->{'fees'}->{'paypay_rate_mul'};
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

function make_full_table() {
	$cfg = get_config();
	$order = get_order();
	$order_items = make_order_items_array($cfg, $order);

	$tbl = "<table>\n";

	$tbl .= make_hdr("#", "Name", "Amount");
	assert($tbl != null);

	foreach ($order_items as $arr_idx => $arr_row) {
		$tbl .= row_add($arr_row[0], $arr_row[1], $arr_row[2]);
	}
	assert($tbl != null);

	$tbl .= "</table>\n";

	return $tbl;
}

function unit_test() {
	$tbl = make_full_table();
}


?>
