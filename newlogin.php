<?php
require 'auth/db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'Developper'; // default role

    $stmt = $pdo->prepare("INSERT INTO User_db(username,email,password,his_role)
                           VALUES(?,?,?,?)");
    $stmt->execute([$username,$email,$password,$role]);

    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
<title>add new user</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/style.php">
</head>

<body class="login-page">
<div class="login-container">
    <form method="POST" class="login-form">
        <div class="login-header">
            <h2>R E G I S T E R   N E W   U S E R</h2>
        </div>
        <input name="username" placeholder="Username" required>
        <input name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
</div>
<a href="login.php" class="btn-back">Back</a>

</body>

</html>