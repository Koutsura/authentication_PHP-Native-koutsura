<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<h1>Selamat datang, Admin!</h1>
<p>Ini adalah halaman dashboard admin.</p>
<a href="logout.php">Logout</a>
<!-- Admin dapat menambahkan fitur seperti melihat daftar mahasiswa, mengelola pengguna, dll. -->
