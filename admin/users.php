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

<a href="../index.php" class="action-btn btn-back">← Prompt Dashboard</a>

<a href="add_user.php" class="action-btn btn-add">Add User</a>

<br><br>

<table>

<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>Created at</th>
<th>Actions</th>
</tr>

<?php foreach($users as $u): ?>

<tr>

<td><?= $u['id'] ?></td>

<td><?= htmlspecialchars($u['username']) ?></td>

<td><?= htmlspecialchars($u['email']) ?></td>

<td><?= htmlspecialchars($u['role']) ?></td>

<td>
    <span class="status-badge <?= $u['status']=='Enabled' ? 'status-approved' : 'status-rejected' ?>">
        <?= $u['status'] ?>
    </span>
</td>

<td><?= htmlspecialchars($u['created_at']) ?></td>

<td class="actions">
<?php if($u['id'] != 1): ?>
<a href="edit_user.php?id=<?= $u['id'] ?>" class="action-btn btn-edit">
Edit
</a>

<form method="POST" action="toggle_user.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= $u['id'] ?>">
    <button class="action-btn btn-delete">
        <?= $u['status']=='Enabled' ? 'Disable' : 'Enable' ?>
    </button>
</form>
<?php endif; ?>
</td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>