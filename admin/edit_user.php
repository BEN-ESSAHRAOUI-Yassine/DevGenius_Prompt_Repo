<?php

require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()){
die("Access denied");
}

$id=$_GET['id'];

$stmt=$pdo->prepare("SELECT * FROM users WHERE id=:id");
$stmt->execute(['id'=>$id]);

$user=$stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=='POST'){

$email=$_POST['email'];
$role=$_POST['role'];

$sql="UPDATE users
SET email=:email, his_role=:role
WHERE id=:id";

$stmt=$pdo->prepare($sql);

$stmt->execute([
'email'=>$email,
'role'=>$role,
'id'=>$id
]);

header("Location: users.php");
exit;

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit user</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>
<h2>Edit User</h2>

<a href="users.php" class="btn-back">← Back to Users</a>

<form method="POST">

<p>Username: <?= htmlspecialchars($user['username']) ?></p>

<input name="email" value="<?= htmlspecialchars($user['email']) ?>">

<br><br>

<select name="role">

<option value="Admin" <?= $user['his_role']=='Admin'?'selected':'' ?>>Admin</option>
<option value="developper" <?= $user['his_role']=='developper'?'selected':'' ?>>Developper</option>

</select>

<br><br>

<button>Update User</button>

</form>
</body>
</html>