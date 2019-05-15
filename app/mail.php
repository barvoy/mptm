<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/Exception.php';
require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';

function email_make_base($cfg, $order, string $body, string $altbody) {
	try {
		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

		//Server settings
		$mail->SMTPDebug = 2;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $cfg['smtp']{'server'}{'host'};
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $cfg['smtp']{'server'}{'username'};
		$mail->Password = $cfg['smtp']{'server'}{'password'};
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = $cfg['smtp']{'server'}{'port'};
		$mail->setFrom(
			$cfg['smtp']{'compose'}{'from_email'},
			$cfg['smtp']{'compose'}{'from_name'}
		);
		$mail->addReplyTo(
			$cfg['smtp']{'compose'}{'reply_to_email'},
			$cfg['smtp']{'compose'}{'reply_to_name'}
		);
		$mail->addBCC($cfg['smtp']{'compose'}{'bcc'});
		$mail->addBCC($cfg['smtp']{'compose'}{'bcc2'});

		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Body    = $body;
		$mail->AltBody = $altbody;
	} catch (Exception $e) {
		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	}
	return $mail;
}

function email_make_welcome($cfg, $order, string $body, string $altbody) {
	$mail = email_make_base($cfg, $order, $body, $altbody);
	$mail->addAddress(
		$order['email']
	);
	$mail->addAttachment(
		$cfg['smtp']{'compose'}{'path'},
		"mptm_new_member.pdf"
	);
	$mail->addAttachment(
		'mptm_invite.ics'
	);
	$mail->Subject = '[MPTM] Welcome to Menlo Park Toastmasters!';

	return $mail;
}

function email_make_reminder($cfg, $order, string $body, string $altbody) {
	$mail = email_make_base($cfg, $order, $body, $altbody);
	$mail->addAddress(
		$order['email']
	);
	$mail->Subject = '[ACTION] Subscribe to the weekly mailing list';

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
