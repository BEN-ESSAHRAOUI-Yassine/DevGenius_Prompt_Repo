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

/* ---------- FETCH PROMPT ---------- */
$stmt = $pdo->prepare("SELECT * FROM prompts WHERE id=?");
$stmt->execute([$id]);
$prompt = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prompt) die("Prompt not found");

/* ---------- OWNER CHECK ---------- */
if(!canEditPrompts($prompt['user_id'])) {
    die("Access denied");
}

/* ---------- STATUS CONTROL ---------- */
$allStatuses = ['Approved','Rejected','Deployed'];

if(isAdmin()){
    $statuses = $allStatuses;
} else {
    $statuses = ['Approved']; // dev restricted
}

/* ---------- CATEGORIES ---------- */
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

/* ---------- FORM SUBMIT ---------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? '';

    /* ---------- VALIDATION ---------- */
    if($title === '') $errors[] = "Title required";
    if($content === '') $errors[] = "Content required";
    if($category === '') $errors[] = "Category required";

    /* SECURITY: enforce allowed statuses */
    if(!in_array($status, $statuses)){
        $errors[] = "Invalid status selection";
    }

    /* ---------- UPDATE ---------- */
    if(empty($errors)){

        $stmt = $pdo->prepare("
            UPDATE prompts 
            SET title=?, content=?, category_id=?, status=? 
            WHERE id=?
        ");

        $stmt->execute([
            $title,
            $content,
            $category,
            $status,
            $id
        ]);

        header("Location: ../index.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Prompt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>

<body>

<div class="form-container">

    <h1>Edit Prompt</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="asset-form" method="POST">

        <!-- TITLE -->
        <input name="title"
               placeholder="Title"
               value="<?= htmlspecialchars($_POST['title'] ?? $prompt['title']) ?>">
        <!-- CONTENT -->
        <textarea name="content" placeholder="Prompt content"><?= htmlspecialchars($_POST['content'] ?? $prompt['content']) ?></textarea>
        <select name="category">
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= ($prompt['category_id']==$c['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <!-- STATUS -->
        <?php if(isAdmin()): ?>
        <select name="status">
            <?php foreach($allStatuses as $s): ?>
                <option value="<?= $s ?>"
                    <?= ($prompt['status']==$s)?'selected':'' ?>>
                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <button type="submit" class="btn-submit">Update</button>

    </form>

    <a href="../index.php" class="btn-back">Back</a>

</div>

</body>

</html>