<?php

require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()){
die("Access denied");
}

$users = $pdo->query("SELECT * FROM users")
->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>User Management</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/style.php">

</head>

<body>

<h1>User Management</h1>

<a href="../index.php" class="btn-back">← Back to Dashboard</a>

<a href="add_user.php" class="btn-add">Add User</a>

<br><br>

<table>

<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Created at</th>
<th>Actions</th>
</tr>

<?php foreach($users as $u): ?>

<tr>

<td><?= $u['id'] ?></td>

<td><?= htmlspecialchars($u['username']) ?></td>

<td><?= htmlspecialchars($u['email']) ?></td>

<td><?= htmlspecialchars($u['role']) ?></td>

<td><?= htmlspecialchars($u['created_at']) ?></td>

<td class="actions">

<form method="POST" action="edit_user.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $u['id'] ?>">
    <button class="btn-edit">Edit</button>
</form>

<form method="POST" action="delete_user.php" 
      onsubmit="return confirm('Delete this user?')" 
      style="display:inline;">

    <input type="hidden" name="id" value="<?= $u['id'] ?>">

    <button type="submit" class="btn-delete">
        Delete
    </button>

</form>

</td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>