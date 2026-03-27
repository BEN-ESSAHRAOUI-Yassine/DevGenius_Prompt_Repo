<?php
require '../auth/db.php';
require '../auth/auth.php';

$id = $_GET['id'] ?? null;

if(!$id){
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT prompts.*, 
           users.username,
           categories.name AS category_name
    FROM prompts
    JOIN users ON prompts.user_id = users.id
    JOIN categories ON prompts.category_id = categories.id
    WHERE prompts.id = ?
");

$stmt->execute([$id]);
$prompt = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prompt){
    die("Prompt not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Prompt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>

<div class="form-container">

    <h1><?= htmlspecialchars($prompt['title']) ?></h1>

    <p><strong>Content:</strong></p>
    <p><?= nl2br(htmlspecialchars($prompt['content'])) ?></p>

    <br>

    <p><strong>Status:</strong> 
        <span class="status-badge status-<?= strtolower($prompt['status']) ?>">
            <?= $prompt['status'] ?>
        </span>
    </p>

    <p><strong>Category:</strong> 
        <?= htmlspecialchars($prompt['category_name']) ?>
    </p>

    <p><strong>Developer:</strong> 
        <?= htmlspecialchars($prompt['username']) ?>
    </p>

    <p><strong>Created at:</strong> 
        <?= $prompt['created_at'] ?>
    </p>

    <br>

    <a href="../index.php" class="btn-back">Back</a>

</div>

</body>
</html>