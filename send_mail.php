<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your@gmail.com';         
        $mail->Password = 'your password 2FA';            
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Penerima dan pengirim
        $mail->setFrom('your@gmail.com', 'HKI Universitas Bina Darma');
        $mail->addAddress($email);

        // Isi email
        $mail->isHTML(true);
        $mail->Subject = 'Kode OTP Anda';
        $mail->Body    = "Kode OTP Anda adalah: <b>$otp</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
