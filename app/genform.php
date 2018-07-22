<?php

declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');

fail_on_error();

$debug = 0;

state_init();
state_trans_from_to('form', 'genform.php');

// ---------------------------------------------------------

if ($debug) {
	$j_str = json_encode($_POST, JSON_PRETTY_PRINT);
	$fd = fopen("/tmp/tmp.txt", "w");
	fwrite($fd, $j_str); //, count($j_str));
	fclose($fd);
}


$mail = email_with_form_and_pdf($_POST);
email_send($mail);

$mail2 = email_with_reminder($_POST);
email_send($mail2);

?>
