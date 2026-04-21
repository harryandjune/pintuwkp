<?php
session_start();
if($_SESSION['role'] != "admin"){
    header("location:../login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - PINTU WKP</title>
</head>
<body>
    <h1>Selamat Datang, Admin <?php echo $_SESSION['nama']; ?>!</h1>
    <p>Ini adalah halaman utama pengelolaan PINTU WKP.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>