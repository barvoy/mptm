#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
fail_on_error();

print out_dir_name();
print has_enough_disk_space();
print make_random_fn();
print make_out_fn();

print_r(pre_check_all());

json_file_save([1,2,3], "nums.json", -1);
$j = load_json_file("nums.json");
assert($j == [1,2,3]);

$order = get_order("../data/data.txt");
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
print_r(club_get_by_name($cfg, 'bleh'));
echo "\n";

$rep = pdf_report_make($cfg, $order_items);
echo $rep;

?>
