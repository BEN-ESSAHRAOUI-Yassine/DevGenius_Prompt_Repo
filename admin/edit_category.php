<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()) die("Access denied");

$id = $_GET['id'] ?? null;
if(!$id) {
    header("Location: categories.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$category) die("Category not found");

$errors = [];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name'] ?? '');
    if($name === '') $errors[] = "Category name required";

    if(empty($errors)){
        $stmt = $pdo->prepare("UPDATE categories SET name=? WHERE id=?");
        $stmt->execute([$name,$id]);
        header("Location: categories.php"); exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>
<h2>Edit Category</h2>

<form method="POST" class="form-container asset-form">
    <input name="name" value="<?= htmlspecialchars($_POST['name'] ?? $category['name']) ?>">
    <button class="action-btn btn-submit">Update Category</button>
</form>

<?php if(!empty($errors)): ?>
<div class="error-box">
    <?php foreach($errors as $err): ?>
        <p><?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<a href="categories.php" class="action-btn btn-back">Back</a>
</body>
</html>