<?php
session_start();
require 'db.php';
require 'send_mail.php';
require 'functions.php'; // Pastikan file functions.php di-require untuk fungsi generateOTP() dan sendOTP()

$message = "";

// Pastikan session registrasi ada
if (!isset($_SESSION['register'])) {
    header("Location: register.php");
    exit;
}

$register = $_SESSION['register'];
$email = $register['email'];
$otp_from_email = $register['otp'];
$expires = $register['expires'];

// Cek jika ada permintaan kirim ulang OTP
if (isset($_GET['resend']) && $_GET['resend'] == 1) {
    // Generate OTP baru dan simpan ke session
    $new_otp = generateOTP();
    $new_expires = time() + 600; // OTP berlaku selama 10 menit

    // Update OTP baru dan waktu kadaluarsa ke session
    $_SESSION['register']['otp'] = $new_otp;
    $_SESSION['register']['expires'] = $new_expires;

    // Kirim OTP baru ke email
    if (sendOTP($email, $new_otp)) {
        $message = "Kode OTP baru telah dikirim ke email Anda.";
    } else {
        $message = "Gagal mengirim ulang OTP. Coba lagi nanti.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp_input = trim($_POST["otp"]);

    // Mengecek apakah OTP yang dimasukkan sama dengan OTP yang disimpan di session
    if ($otp_input == $otp_from_email) {
        // Mengecek apakah OTP sudah kadaluarsa
        if (time() < $expires) {
            // OTP valid, simpan ke database dan verifikasi akun
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $register['email'], $register['password'], $register['role']);

            if ($stmt->execute()) {
                // Menghapus data sementara setelah registrasi berhasil
                unset($_SESSION['register']);
                $message = "Verifikasi berhasil. Akun Anda telah dibuat.";

                // Simpan data pengguna ke session untuk login otomatis
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $register['role'];

                // Redirect ke dashboard setelah verifikasi berhasil
                if ($register['role'] === 'admin') {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $message = "Gagal menyimpan ke database.";
            }
        } else {
            $message = "OTP sudah kadaluarsa.";
        }
    } else {
        $message = "OTP salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi OTP</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header text-center">
          <h4>Verifikasi OTP</h4>
        </div>
        <div class="card-body">
          <?php if ($message): ?>
            <div class="alert alert-info text-center"><?= $message ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label for="otp" class="form-label">Kode OTP</label>
              <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <div class="d-grid mb-2">
              <button type="submit" class="btn btn-primary">Verifikasi</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <a href="?resend=1">Kirim ulang kode OTP</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
