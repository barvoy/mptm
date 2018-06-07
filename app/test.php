#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
fail_on_error();

echo "# trying to load config.json\n";
$tmp = load_json_file("config.json");
assert($tmp != NULL);

echo "# trying to load mail_conf.yml\n";
$mail_conf = load_yaml_file("mail_conf.yml");
assert($mail_conf != NULL);

echo "# trying to load data.txt\n";
$pseudo_post = load_json_file("sample_data/data.txt");
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

$rep = html_report_make($cfg, $order_items, $order);
echo $rep;

?>
