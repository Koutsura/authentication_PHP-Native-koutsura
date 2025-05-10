<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendPasswordResetEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ryumaakana@gmail.com'; // Ganti dengan email kamu
        $mail->Password = 'xewwupuozawbzaou'; // Ganti dengan app password kamu
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Penerima dan pengirim
        $mail->setFrom('ryumaakana@gmail.com', 'HKI Universitas Bina Darma');
        $mail->addAddress($email);

        // Isi email
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password - Kode OTP';
        $mail->Body    = "
            <p>Untuk mereset password akun Anda, gunakan kode OTP berikut:</p>
            <h2>$otp</h2>
            <p>OTP ini berlaku selama 10 menit.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        ";

        // Mengirimkan email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
