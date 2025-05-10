<?php
// login.php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_pass, $role);

    if ($stmt->fetch()) {
        if (password_verify($password, $db_pass)) {
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header("Location: dashboard_admin.php");
                exit;
            } else {
                header("Location: dashboard.php");
                exit;
            }
        } else {
            $error = "Login gagal. Cek kembali email dan kata sandi.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Sistem</title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4">Login</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="email" class="form-label">Alamat Email</label>
        <input type="email" name="email" class="form-control" id="email" required autofocus>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" id="password" required>
      </div>

      <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">Masuk</button>
      </div>

      <div class="text-center">
        <a href="forgot_password.php">Lupa Password?</a>
      </div>
      <div class="text-center">
        <a href="register.php">Belum punya akun? Buat akun sekarang.</a>
      </div>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
