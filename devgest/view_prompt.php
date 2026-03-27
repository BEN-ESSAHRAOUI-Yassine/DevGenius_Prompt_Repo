<?php
require '../auth/db.php';
require '../auth/auth.php';
require '../auth/role.php';

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
    <div class="content-box">
        <?= nl2br(htmlspecialchars($prompt['content'])) ?>
    </div>

    <br>

    <div class="meta-grid">

        <div><strong>Status:</strong><br>
            <span class="status-badge status-<?= strtolower($prompt['status']) ?>">
                <?= $prompt['status'] ?>
            </span>
        </div>

        <div><strong>Category:</strong><br>
            <?= htmlspecialchars($prompt['category_name']) ?>
        </div>

        <div><strong>Developer:</strong><br>
            <?= htmlspecialchars($prompt['username']) ?>
        </div>

        <div><strong>Created at:</strong><br>
            <?= $prompt['created_at'] ?>
        </div>

    </div>

    <br>

    <div class="actions" style="margin-top:20px;">
        <a href="../index.php" class="action-btn btn-back">Back</a>
        <!-- COPY BUTTON -->
        <button onclick="copyPrompt()" class="action-btn btn-copy">
            Copy Prompt
        </button>

        <?php if(
            canEditPrompts($prompt['user_id']) &&
            !(isDevelopper() && $prompt['status'] === 'Deployed')
        ): ?>

            <!-- EDIT -->
            <a href="update_prompt.php?id=<?= $prompt['id'] ?>" class="action-btn btn-edit"> Edit prompt</a>

            <!-- DELETE -->
            <form method="POST" action="delete_prompt.php"
                onsubmit="return confirm('Delete this prompt?')"
                style="display:inline;">

                <input type="hidden" name="id" value="<?= $prompt['id'] ?>">
                <button type="submit" class="action-btn btn-delete">Delete Prompt</button>

            </form>

        <?php endif; ?>

    </div>
</div>
<script>
function copyPrompt(){
    const title = <?= json_encode($prompt['title']) ?>;
    const content = <?= json_encode($prompt['content']) ?>;

    const text = `PROMPT TITLE: ${title}\n\nPROMPT CONTENT: ${content}`;

    navigator.clipboard.writeText(text)
        .then(() => alert("Copied to clipboard!"))
        .catch(err => console.error(err));
}
</script>
</body>
</html>