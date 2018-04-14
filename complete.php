<?
error_reporting(E_STRICT);


include_once('lib.php');
state_init();
state_trans_from_to('save', 'complete');

print $_SESSION['fn'];

?>
