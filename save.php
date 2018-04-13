<?php

include_once('lib.php');

state_init();
state_trans_from_to('form', 'save');

$sub_data = array(
	"post" => $_POST
	,
	"get" => $_GET
	,
	"server" => $_SERVER
	,
	"cookie" => $_COOKIE
	,
	"request" => $_REQUEST
);

$j_str = json_encode($sub_data, JSON_PRETTY_PRINT);
$fn_out = make_out_fn();
$fp = fopen(out_dir_name() . "/" . $fn_out, 'w');
fwrite($fp, $j_str);
fclose($fp);

$_SESSION['fn'] = $fn_out;

header("Location: /complete.php");

?>
