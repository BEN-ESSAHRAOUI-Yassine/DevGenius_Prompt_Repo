<?php
session_start();
require 'auth/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = "SELECT * FROM users WHERE username = :username";

$stmt = $pdo->prepare($sql);
$stmt->execute(['username'=>$username]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password,$user['password'])){

    if($user['status'] !== 'Enabled'){
        $error = "Account is disabled";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit;
    }
}else{
    $error = "Invalid username or password";
}

}

?>

<!DOCTYPE html>
<html>

<head>
<title> DevGenius Solutions:Prompt Repository - Login</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/style.php">
</head>

<body class="login-page">

<div class="login-container">
    <form method="POST" class="login-form">
        <div class="login-header">
            <h1>DevGenius Solutions:<br> Prompt Repository</h1>
            <br>
            <h2>LOGIN PAGE</h2>
        </div>
        <?php if($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <input name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">LOGIN</button>
        <br><br>
        <a href="newlogin.php" class="btn-back" style="display:block; text-align:center;">
            Create New Account
        </a>
    </form>
</div>

</body>

</html>