<?php
declare(strict_types=1);
error_reporting(-1);


include_once('lib.php');

fail_on_error();

state_init();
state_trans_from_to('save', 'complete');

print $_SESSION['fn'];

?>
