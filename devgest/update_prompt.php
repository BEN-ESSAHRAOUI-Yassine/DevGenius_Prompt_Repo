<?php

require '../auth/db.php';
require '../auth/auth.php';
require '../auth/role.php';

if(!canEditPrompts()){
die("Access denied");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM prompts WHERE id=?");
$stmt->execute([$id]);
$prompt = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prompt) die("Prompt not found");
if(!canEditPrompts($prompt['user_id'])) die("Access denied");
$allStatuses = ['Approved','Rejected','Deployed'];
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
if(isAdmin()){
    $statuses = $allStatuses; // Admin can pick any status
} else {
    $statuses = ['Approved']; // Developers cannot deploy/reject
}
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? '';

    if($title === '') $errors[] = "Title required";
    if($content === '') $errors[] = "Content required";
    if($category === '') $errors[] = "Category required";
    if($status === '' || !in_array($status, $statuses)) $errors[] = "Valid status required";

    if(empty($errors)){
        $stmt = $pdo->prepare("UPDATE prompts SET title=?, content=?, category_id=? , status=? WHERE id=?");
        $stmt->execute([$title,$content,$category, $status,$id]);
        header("Location: ../index.php"); exit;
    }

}

?>

<!DOCTYPE html>
<html>

<head>
<title>Edit a saved Prompt</title>
<link rel="stylesheet" href="../assets/css/style.php">
</head>

<body>

<div class="form-container">

    <h1>Edit a saved Prompt</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="asset-form" method="POST">

        <input name="title" value="<?= htmlspecialchars($_POST['title'] ?? $prompt['title']) ?>">
        <textarea name="content"><?= htmlspecialchars($_POST['content'] ?? $prompt['content']) ?></textarea>
        <select name="category">
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($prompt['category_id']==$c['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <?php foreach($statuses as $s): ?>
                <option value="<?= $s ?>" <?= ($prompt['status']==$s)?'selected':'' ?>>
                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-submit">Update</button>

    </form>

    <a href="../index.php" class="btn-back">Back</a>

</div>

</body>

</html>