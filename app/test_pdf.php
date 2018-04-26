#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
fail_on_error();

$order = get_order("data.txt");
assert($order != NULL);
$cfg = get_config();
assert($cfg != NULL);
$order_items = make_order_items_array($cfg, $order);
assert($order_items != NULL);

$rep = pdf_report_make($cfg, $order_items);

pdf_generate($rep);

?>
