#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');

print out_dir_name();
print has_enough_disk_space();
print make_random_fn();
print make_out_fn();

print_r(pre_check_all());

json_file_save([1,2,3], "nums.json");
$j = load_json_file("nums.json");
assert($j == [1,2,3]);

print make_full_table("data.txt");
?>
