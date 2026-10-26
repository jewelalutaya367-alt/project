<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

function sendSystemEmail(string $recipientEmail, string $recipientName, string $subject, string $messageBody) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'SMTP.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jewelalutaya367@gmail.com';
        $mail->Password   = 'mvefsytbwpwgfzhr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('jewelalutaya367@gmail.com', 'Laboratory Inventory System');
        $mail->addAddress($recipientEmail, $recipientName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>