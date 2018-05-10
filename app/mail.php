<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';

function email_make($cfg, string $body, string $altbody) {
	try {
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

		//Server settings
		$mail->SMTPDebug = 2;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $cfg['smtp']->{'server'}->{'host'};
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $cfg['smtp']->{'server'}->{'username'};
		$mail->Password = $cfg['smtp']->{'server'}->{'password'};
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $cfg['smtp']->{'server'}->{'port'};
		$mail->setFrom(
			$cfg['smtp']->{'compose'}->{'from_email'},
			$cfg['smtp']->{'compose'}->{'from_name'}
		);
		$mail->addAddress(
			$cfg['smtp']->{'compose'}->{'to_email'},
			$cfg['smtp']->{'compose'}->{'to_name'}
		);
		$mail->addReplyTo(
			$cfg['smtp']->{'compose'}->{'reply_to_email'},
			$cfg['smtp']->{'compose'}->{'reply_to_name'}
		);
		$mail->addBCC($cfg['smtp']->{'compose'}->{'bcc'});
		$mail->addAttachment(
			$cfg['smtp']->{'compose'}->{'path'},
			"mptm_new_member.pdf"
		);

		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = '[MPTM] Welcome to Menlo Park Toastmasters!';
		$mail->Body    = $body;
		$mail->AltBody = $altbody;
	} catch (Exception $e) {
		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	}
	return $mail;
}

function email_send($mail) {
	try {
		$mail->send();
		echo 'Message has been sent';
	} catch (Exception $e) {
		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	}
}


?>
