<?php
session_start();
require 'functions.php';
require 'send_password.php'; // Pastikan file ini berisi fungsi `sendPasswordResetEmail($email, $otp)`

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $otp = generateOTP(); // Fungsi untuk generate OTP

    // Simpan OTP dan email ke session
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_otp'] = $otp;
    $_SESSION['reset_otp_expires'] = time() + (10 * 60); // 10 menit dari sekarang

    if (sendPasswordResetEmail($email, $otp)) { // Menggunakan fungsi sendPasswordResetEmail dari send_password.php
        header("Location: password_otp.php");
        exit();
    } else {
        $message = "Gagal mengirim kode OTP ke email. Pastikan email valid dan SMTP aktif.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4">Lupa Password</h2>

    <?php if (!empty($message)): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="email" class="form-label">Alamat Email</label>
        <input type="email" name="email" class="form-control" id="email" required autofocus>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Kirim Kode OTP</button>
      </div>

      <div class="mt-3 text-center">
        <a href="login.php">Kembali ke Login</a>
      </div>
    </form>
  </div>
</body>
</html>
