<?php
declare(strict_types=1);
error_reporting(-1);

include_once('lib.php');

state_init();
state_trans_from_to('form', 'save');

$sub_data = array(
	"post" => $_POST
	,
	"get" => $_GET
);

$fn = out_dir_name() . "/" . make_out_fn();
json_file_save($sub_data, $fn);

$_SESSION['fn'] = $fn;

header("Location: /complete.php");

?>
