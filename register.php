<?php
session_start();
require 'db.php';
require 'functions.php'; // Pastikan file functions.php di-require untuk fungsi generateOTP()

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Mengambil data dari form
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirmation = $_POST["password_confirmation"];

    // Validasi konfirmasi kata sandi
    if ($password !== $password_confirmation) {
        $message = "Kata sandi dan konfirmasi kata sandi tidak cocok.";
    } else {
        // Cek kekuatan kata sandi
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[\W_]/', $password) || strlen($password) < 5) {
            $message = "Kata sandi harus mengandung huruf besar, huruf spesial, dan minimal 5 karakter.";
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $otp = generateOTP(); // Menghasilkan OTP menggunakan fungsi yang sudah didefinisikan
            $expires = time() + 600; // OTP berlaku selama 10 menit
            $role = 'mahasiswa'; // Default role

            // Menyimpan data sementara di session sebelum verifikasi
            $_SESSION['register'] = [
                'email' => $email,
                'password' => $password,
                'otp' => $otp,
                'expires' => $expires,
                'role' => $role
            ];

            // Kirim OTP ke email
            require 'send_mail.php';
            if (sendOTP($email, $otp)) {
                // Redirect ke halaman verifikasi OTP setelah OTP dikirim
                header("Location: verify_otp.php");
                exit;
            } else {
                $message = "Gagal mengirim OTP. Coba lagi nanti.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Akun</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function checkPasswordStrength() {
        var password = document.getElementById("password").value;
        var strength = document.getElementById("strength");
        var bar = document.getElementById("progress-bar");
        var score = 0;

        // Cek panjang kata sandi
        if (password.length >= 5) score++;
        // Cek ada huruf besar
        if (/[A-Z]/.test(password)) score++;
        // Cek ada huruf kecil
        if (/[a-z]/.test(password)) score++;
        // Cek ada angka
        if (/\d/.test(password)) score++;
        // Cek ada karakter spesial
        if (/[\W_]/.test(password)) score++;

        // Tentukan kekuatan kata sandi
        switch(score) {
            case 0:
            case 1:
                strength.innerHTML = "Kekuatan: Lemah";
                strength.style.color = "red";
                bar.style.width = "20%";
                bar.className = "progress-bar bg-danger";
                break;
            case 2:
            case 3:
                strength.innerHTML = "Kekuatan: Sedang";
                strength.style.color = "orange";
                bar.style.width = "60%";
                bar.className = "progress-bar bg-warning";
                break;
            case 4:
            case 5:
                strength.innerHTML = "Kekuatan: Kuat";
                strength.style.color = "green";
                bar.style.width = "100%";
                bar.className = "progress-bar bg-success";
                break;
        }
    }
  </script>
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4">Registrasi Akun</h2>

    <?php if (!empty($message)): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="email" class="form-label">Alamat Email</label>
        <input type="email" name="email" class="form-control" id="email" required autofocus>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" id="password" required onkeyup="checkPasswordStrength()">
        <div id="strength"></div>
        <div class="progress">
          <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
      </div>

      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Daftar</button>
      </div>

      <div class="text-center">
        <a href="login.php">Sudah punya akun? Login di sini.</a>
      </div>
    </form>
  </div>

  <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
