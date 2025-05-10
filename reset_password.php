<?php
session_start();
require 'db.php'; // koneksi ke database

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Mengambil data dari form
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

            if (isset($_SESSION['reset_email'])) {
                $email = $_SESSION['reset_email'];

                // Update password di database
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $password, $email);

                if ($stmt->execute()) {
                    // Bersihkan session OTP dan email setelah password berhasil diubah
                    unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_otp_expires']);
                    $message = "Password berhasil diubah. Silakan login.";
                    
                    // Redirect ke halaman login setelah sukses
                    header("Location: login.php");
                    exit();
                } else {
                    $message = "Gagal memperbarui password. Coba lagi.";
                }
            } else {
                $message = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ubah Password</title>
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
    <h2 class="text-center mb-4">Ubah Password</h2>

    <?php if (!empty($message)): ?>
      <div class="alert <?= strpos($message, 'berhasil') !== false ? 'alert-success' : 'alert-danger' ?>" role="alert">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="password" class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-control" id="password" required onkeyup="checkPasswordStrength()">
        <div id="strength"></div>
        <div class="progress">
          <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
      </div>

      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Ubah Password</button>
      </div>
    </form>
  </div>
</body>
</html>
