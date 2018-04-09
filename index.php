<?php
include_once('lib.php');

$ret = pre_check_all();
if ($ret[0] != 0) {
	echo "Ops. Errors!\n";
	echo $ret[1] . "\n";
}

state_init();
state_trans_from_to(null, init);

header("Location: /form.php");

?>
