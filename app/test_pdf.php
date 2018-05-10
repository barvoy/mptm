#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
fail_on_error();

$pseudo_post = load_json_file("sample_data/data.txt");
assert($pseudo_post != NULL);

$order = get_order_from_post($pseudo_post);
assert($order != NULL);

$cfg = get_config();
assert($cfg != NULL);

$order_items = make_order_items_array($cfg, $order);
assert($order_items != NULL);

$rep = html_report_make($cfg, $order_items);
assert($rep != NULL);

pdf_generate($rep);

?>
