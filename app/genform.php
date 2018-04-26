<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');

fail_on_error();

state_init();
state_trans_from_to('save', 'genform.php');

// ---------------------------------------------------------

$fn_in = $_SESSION['fn'];
$order = get_order($fn_in);
assert($order != NULL);
//print_r($order);

$cfg = get_config();
assert($cfg != NULL);

$order_items = make_order_items_array($cfg, $order);
assert($order_items != NULL);

// @todo: tools for php static analysis
// @todo: php switch for strict code checking

$rep = pdf_report_make($cfg, $order_items);
pdf_generate($rep);
?>
