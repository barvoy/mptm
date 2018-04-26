<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');

fail_on_error();

//state_init();
//state_trans_from_to('save', 'genform.php');

$cfg = get_config();
$name = $_GET['alias'];

assert($cfg != NULL);
assert($name != NULL);

$club_info = club_get_by_name($cfg, $name);

$ret_str = "var club_info = " . json_encode($club_info, JSON_PRETTY_PRINT) . ";";

echo $ret_str;

?>
