<?php
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
?>

<h1>Selamat datang, Mahasiswa!</h1>
<p>Ini adalah halaman dashboard mahasiswa.</p>
<a href="logout.php">Logout</a>