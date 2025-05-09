<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

require 'includes/init.php';

$email   = '';
$subject = '';
$message = '';
$sent    = false;

if ("POST" === $_SERVER[ 'REQUEST_METHOD' ]) {
    $email   = $_POST[ 'email' ];
    $subject = $_POST[ 'subject' ];
    $message = $_POST[ 'message' ];

    $errors = [  ];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[  ] = 'Please enter a valid email address';
    }

    if ('' === $subject) {
        $errors[  ] = 'Please enter a subject';
    }

    if ('' === $message) {
        $errors[  ] = 'Please enter a message';
    }

    if (empty($errors)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(SMTP_FROM, 'Mailer');
            $mail->addAddress('joe@example.net', 'Joe User');
            $mail->addReplyTo($email);

            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            $sent = true;
        } catch (Exception $e) {
            echo 'Message not sent: ' . $mail->ErrorInfo;
        }
    }
}

; ?>

<?php require 'includes/header.php'; ?>

<h2>Contact</h2>

<?php if ($sent): ?>
    <p>Message sent.</p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?=$error; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" id="formContact">
    <div class="form-group">
        <label for="email">Your email</label>
        <input name="email" id="email" type="email" placeholder="Your email" class="form-control" value="<?=htmlspecialchars($email); ?>">
    </div>

    <div class="form-group">
        <label for="subject">Subject</label>
        <input name="subject" id="subject" placeholder="Subject" class="form-control" value="<?=htmlspecialchars($subject); ?>">
    </div>

    <div class="form-group">
        <label for="message">Message</label>
        <textarea name="message" id="message" placeholder="Message" class="form-control" value="<?=htmlspecialchars($message); ?>"></textarea>
    </div>

    <button class="btn">Send</button>
</form>

<?php require 'includes/footer.php'; ?>