#!/usr/bin/env php
<?php
declare(strict_types=1);
error_reporting(-1);

require_once('lib.php');
require_once('mail.php');
fail_on_error();

// Imitate _POST request data.
$pseudo_post = load_json_file("sample_data/data.txt");
assert($pseudo_post != NULL);

$mail = email_with_form_and_pdf($pseudo_post);
assert($mail != NULL);
email_send($mail);

$mail2 = email_with_reminder($pseudo_post);
assert($mail2 != NULL);
email_send($mail2);

?>
