#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
require_once('mail.php');
fail_on_error();

//send_email();

$body = "test";
$altbody = "alt test";

$cfg = load_json_file('mail_conf.js');

print_r($cfg);


$m = email_make($cfg, $body, $altbody);
print_r($m);

?>
