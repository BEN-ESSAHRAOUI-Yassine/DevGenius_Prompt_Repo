<?php

require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()){
die("Access denied");
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    
$username=$_POST['username'];
$email=$_POST['email'];

if(empty($username) || empty($email) || empty($_POST['password'])){
    die("All fields required");
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    die("Invalid email");
}

$password1=password_hash($_POST['password'],PASSWORD_DEFAULT);
$role=$_POST['role'];

$sql="INSERT INTO users(username,email,password,role)
VALUES(:u,:e,:p,:r)";

$stmt=$pdo->prepare($sql);

$stmt->execute([
'u'=>$username,
'e'=>$email,
'p'=>$password1,
'r'=>$role
]);

header("Location: users.php");
exit;

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>
<h2>Add User</h2>

<a href="users.php" class="action-btn btn-back">← Back to Users</a>

<form method="POST">

<input name="username" placeholder="Username" required>

<br><br>

<input name="email" placeholder="Email" required>

<br><br>

<input type="password" name="password" placeholder="Password" required>

<br><br>

<select name="role">

<option value="Admin">Admin</option>
<option value="Developper">Developper</option>

</select>

<br><br>

<button class="action-btn btn-submit">Create User</button>

</form>
</body>
</html>