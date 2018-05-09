#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
fail_on_error();

$tmp = load_json_file("config.json");
assert($tmp != NULL);

$pseudo_post = load_json_file("../data/data.txt");
assert($pseudo_post != NULL);

$order = get_order_from_post($pseudo_post);
assert($order != NULL);

$cfg = get_config();
assert($cfg != NULL);

$order_items = make_order_items_array($cfg, $order);
assert($order_items != NULL);

print make_full_table($order_items);

// @todo: commit edu_php

echo "# ------ club search. should get something";
print_r(club_get_by_name($cfg, 'mptm'));
echo "# ------ club search. should get NULL";
print_r(club_get_by_name($cfg, 'bleh') == NULL);
echo "\n";

$rep = pdf_report_make($cfg, $order_items);
echo $rep;

?>
