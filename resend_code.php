<?php
require 'db.php';  // Pastikan sudah ada koneksi database
require 'functions.php';  // Untuk generateOTP() dan fungsi lainnya
require 'send_mail.php';  // Untuk fungsi kirim email OTP

// Mengecek apakah email diteruskan melalui URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Mengecek apakah email ada di database
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email ditemukan, generate OTP baru
        $otp = generateOTP();  // Generate OTP baru
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));  // Tentukan kadaluarsa OTP

        // Menyimpan OTP dan waktu kadaluarsa ke database
        $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE email = ?");
        $stmt->bind_param("sss", $otp, $expires, $email);
        
        if ($stmt->execute()) {
            // Mengirim OTP ke email
            if (sendOTP($email, $otp)) {
                // Berhasil mengirim OTP, redirect ke halaman verifikasi
                header("Location: verify_otp.php?email=" . urlencode($email));
                exit;
            } else {
                echo "Gagal mengirim OTP ke email.";
            }
        } else {
            echo "Gagal memperbarui kode OTP di database.";
        }
    } else {
        echo "Email tidak ditemukan. Silakan periksa kembali email yang Anda masukkan.";
    }

    // Menutup statement
    $stmt->close();
} else {
    echo "Email tidak diberikan.";
}
?>
