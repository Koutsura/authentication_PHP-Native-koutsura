<?php
session_start();
require 'db.php';
require 'send_password.php';
$message = "";

// Fungsi untuk mengirim OTP baru
function resendOtp($email) {
    // Membuat OTP baru
    $otp = rand(100000, 999999);
    $_SESSION['reset_otp'] = $otp;
    $_SESSION['reset_otp_expires'] = time() + 600;  // OTP berlaku selama 10 menit

    // Mengirim OTP ke email
    if (sendPasswordResetEmail($email, $otp)) {
        return "Kode OTP telah dikirim ke email Anda.";
    } else {
        return "Gagal mengirimkan kode OTP. Coba lagi.";
    }
}

// Cek apakah pengguna meminta untuk mengirim ulang OTP
// Cek apakah pengguna meminta untuk mengirim ulang OTP (hanya jika metode GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['resend']) && $_GET['resend'] == 1) {
    // Pastikan email ada di session atau dikirim kembali ke sini
    if (isset($_SESSION['reset_email'])) {
        $message = resendOtp($_SESSION['reset_email']);
    } else {
        $message = "Email tidak ditemukan. Silakan coba lagi.";
    }
}

// Proses verifikasi OTP
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_otp = trim($_POST['otp'] ?? ''); // Menghapus spasi ekstra dari input

    if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_otp_expires'])) {
        $message = "Kode OTP tidak tersedia. Silakan kirim ulang dari halaman lupa password.";
    } elseif (time() > $_SESSION['reset_otp_expires']) {
        $message = "Kode OTP telah kedaluwarsa. Silakan kirim ulang.";
    } elseif ((string)$input_otp === (string)$_SESSION['reset_otp']) {
        // Jika OTP benar, arahkan ke halaman reset password
        header("Location: reset_password.php");
        exit();
    } else {
        $message = "Kode OTP salah. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi OTP</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4">Verifikasi OTP</h2>

    <?php if (!empty($message)): ?>
      <div class="alert alert-info" role="alert">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="otp" class="form-label">Kode OTP</label>
        <input type="text" class="form-control" id="otp" name="otp" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Verifikasi</button>
      </div>

      <div class="mt-3 text-center">
        <a href="?resend=1">Kirim ulang kode OTP</a>
      </div>
    </form>
  </div>

</body>
</html>
