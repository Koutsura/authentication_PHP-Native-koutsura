<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Portal Autentikasi</title>
</head>
<body>

<?php
if (isset($_SESSION['role'])) {
    // Redirect sesuai role
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] === 'mahasiswa') {
        header("Location: dashboard.php");
        exit;
    }
}
?>

<h2>Selamat datang di Sistem Autentikasi</h2>
<p>Silakan Login atau Register terlebih dahulu.</p>

<a href="login.php">Login</a> |
<a href="register.php">Register</a>

</body>
</html>
