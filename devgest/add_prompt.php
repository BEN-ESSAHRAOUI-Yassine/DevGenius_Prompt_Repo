<?php

require '../auth/db.php';
require '../auth/auth.php';
require '../auth/role.php';

if(!isAdmin() && !isDevelopper()){
die("Access denied");
}

$categories = $pdo->query("SELECT * FROM categories")
->fetchAll(PDO::FETCH_ASSOC);
$errors = [];

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? '';

    if($title === '') $errors[] = "Title required";
    if($content === '') $errors[] = "Content required";
    if($category === '') $errors[] = "Category required";

    if(empty($errors)){
        $stmt = $pdo->prepare("INSERT INTO prompts(title,content,category_id,user_id) VALUES(?,?,?,?)");
        $stmt->execute([$title,$content,$category,$_SESSION['user_id']]);
        header("Location: ../index.php"); exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Add Prompt</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/style.php">
</head>

<body>
<div class="form-container">
<h1>Add Prompt</h1>

<form class="asset-form" method="POST">

    <input name="title" placeholder="Prompt Title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    <textarea name="content" placeholder="Prompt Content"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
    <select name="category">
        <option value="">Select Category</option>
        <?php foreach($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (($_POST['category'] ?? '')==$c['id'])?'selected':'' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn-submit">Add</button>

</form>

<?php if(!empty($errors)): ?>

<div class="error-box">

<?php foreach($errors as $error): ?>

<p><?= htmlspecialchars($error) ?></p>

<?php endforeach; ?>

</div>

<?php endif; ?>

<a href="../index.php" class="btn-back">Back</a>
</div>
</body>
</html>