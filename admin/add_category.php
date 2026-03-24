<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()) die("Access denied");

$errors = [];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name'] ?? '');
    if($name === '') $errors[] = "Category name required";

    if(empty($errors)){
        $stmt = $pdo->prepare("INSERT INTO categories(name) VALUES(?)");
        $stmt->execute([$name]);
        header("Location: categories.php"); exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>
<h2>Add Category</h2>

<form method="POST" class="form-container asset-form">
    <input name="name" placeholder="Category Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    <button class="btn-submit">Add Category</button>
</form>

<?php if(!empty($errors)): ?>
<div class="error-box">
    <?php foreach($errors as $err): ?>
        <p><?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<a href="categories.php" class="btn-back">Back</a>
</body>
</html>