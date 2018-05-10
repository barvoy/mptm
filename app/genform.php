<?php

declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');

fail_on_error();

$debug = 0;

//state_init();
//state_trans_from_to('form', 'genform.php');

// ---------------------------------------------------------

if ($debug) {
	$j_str = json_encode($_POST, JSON_PRETTY_PRINT);
	$fd = fopen("/tmp/tmp.txt", "w");
	fwrite($fd, $j_str); //, count($j_str));
	fclose($fd);
}

$order = get_order_from_post($_POST);
assert($order != NULL);

$cfg = get_config();
assert($cfg != NULL);

$order_items = make_order_items_array($cfg, $order);
assert($order_items != NULL);

// @todo: tools for php static analysis
// @todo: php switch for strict code checking

$rep = pdf_report_make($cfg, $order_items);
pdf_generate($rep);

send_mail();

?>
