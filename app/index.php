<?php
declare(strict_types=1);
error_reporting(-1);

include_once('lib.php');

fail_on_error();

$ret = pre_check_all();
if ($ret[0] != 0) {
	echo "Ops. Errors!\n";
	echo $ret[1] . "\n";
	exit(0);
}

state_init();
state_trans_from_to(null, "init");

header("Location: /form.php");
?>
