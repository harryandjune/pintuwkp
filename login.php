<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // Menggunakan MD5 sesuai dummy data sebelumnya

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Set Session
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['nama']    = $data['nama_lengkap'];
        $_SESSION['role']    = $data['role'];
        $_SESSION['status']  = "login";

        // Pengalihan berdasarkan role
        if ($data['role'] == "admin") {
            header("location:admin/index.php");
        } else {
            header("location:user/index.php");
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - PINTU WKP</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; border: none; color: white; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>PINTU WKP</h2>
        <p style="text-align:center; font-size:12px;">Pusat Informasi & Tata Usaha WKP</p>
        
        <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>