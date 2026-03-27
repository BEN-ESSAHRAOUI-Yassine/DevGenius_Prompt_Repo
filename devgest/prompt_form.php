<?php
require '../auth/db.php';
require '../auth/auth.php';
require '../auth/role.php';

/* ========================
   MODE DETECTION
======================== */
$id = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? 'add'; 
// modes: add | view | edit | delete

$prompt = null;

/* ========================
   FETCH PROMPT (if exists)
======================== */
if ($id) {
    $stmt = $pdo->prepare("
        SELECT prompts.*, users.username, categories.name AS category_name
        FROM prompts
        JOIN users ON prompts.user_id = users.id
        JOIN categories ON prompts.category_id = categories.id
        WHERE prompts.id = ?
    ");
    $stmt->execute([$id]);
    $prompt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prompt) {
        die("Prompt not found");
    }

    // default mode if id exists
    if (!isset($_GET['mode'])) {
        $mode = 'view';
    }
}

/* ========================
   PERMISSIONS
======================== */
$canEdit = $prompt && canEditPrompts($prompt['user_id']) &&
          !(isDevelopper() && $prompt['status'] === 'Deployed');

/* ========================
   CATEGORIES
======================== */
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

/* ========================
   FORM SUBMIT
======================== */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    /* ===== ADD ===== */
    if ($action === 'add') {

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? '';

        if ($title === '') $errors[] = "Title required";
        if ($content === '') $errors[] = "Content required";
        if ($category === '') $errors[] = "Category required";

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                INSERT INTO prompts(title,content,category_id,user_id)
                VALUES(?,?,?,?)
            ");
            $stmt->execute([$title,$content,$category,$_SESSION['user_id']]);

            header("Location: ../index.php");
            exit;
        }
    }

    /* ===== EDIT ===== */
    if ($action === 'edit' && $prompt && $canEdit) {

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category = $_POST['category'] ?? '';

        if ($title === '') $errors[] = "Title required";
        if ($content === '') $errors[] = "Content required";
        if ($category === '') $errors[] = "Category required";

        if (empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE prompts 
                SET title=?, content=?, category_id=?
                WHERE id=?
            ");
            $stmt->execute([$title,$content,$category,$id]);

            // return to VIEW mode after update
            header("Location: prompt_form.php?id=$id&mode=view");
            exit;
        }
    }

    /* ===== DELETE ===== */
    if ($action === 'delete' && $prompt && $canEdit) {

        $stmt = $pdo->prepare("DELETE FROM prompts WHERE id=?");
        $stmt->execute([$id]);

        header("Location: ../index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prompt</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="form-container">

<!-- ========================
     TITLE
======================== -->
<h1>
    <?php if ($mode === 'add'): ?>
        Add Prompt
    <?php else: ?>
        <?= htmlspecialchars($prompt['title']) ?>
    <?php endif; ?>
</h1>

<!-- ========================
     ERROR BOX
======================== -->
<?php if (!empty($errors)): ?>
<div class="error-box">
    <?php foreach ($errors as $e): ?>
        <p><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ========================
     FORM
======================== -->
<form class="asset-form" method="POST">

<?php
$isReadonly = ($mode === 'view' || $mode === 'delete');
?>

<!-- TITLE -->
<input name="title"
       value="<?= htmlspecialchars($_POST['title'] ?? $prompt['title'] ?? '') ?>"
       placeholder="Title"
       <?= $isReadonly ? 'readonly' : '' ?>>

<!-- CONTENT -->
<textarea name="content" class="content-box"
    placeholder="Content"
    <?= $isReadonly ? 'readonly' : '' ?>
><?= htmlspecialchars($_POST['content'] ?? $prompt['content'] ?? '') ?></textarea>

<!-- CATEGORY -->
<select name="category" <?= $isReadonly ? 'disabled' : '' ?>>
    <option value="">Select Category</option>
    <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>"
            <?= (($prompt['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<!-- ========================
     VIEW MODE (INFO)
======================== -->
<?php if ($prompt && $mode === 'view'): ?>
<div class="meta-grid">

    <div><strong>Status:</strong><br>
        <span class="status-badge status-<?= strtolower($prompt['status']) ?>">
            <?= $prompt['status'] ?>
        </span>
    </div>

    <div><strong>Developer:</strong><br>
        <?= htmlspecialchars($prompt['username']) ?>
    </div>

    <div><strong>Created:</strong><br>
        <?= $prompt['created_at'] ?>
    </div>

</div>
<?php endif; ?>

<!-- ========================
     ACTION BUTTONS
======================== -->
<div class="actions">

<!-- BACK -->
<a href="../index.php" class="action-btn btn-back">Back</a>

<?php if ($mode === 'view'): ?>

    <!-- COPY -->
    <button type="button" onclick="copyPrompt()" class="action-btn btn-copy">
        Copy
    </button>

    <?php if ($canEdit): ?>

        <!-- EDIT -->
        <a href="?id=<?= $id ?>&mode=edit" class="action-btn btn-edit">Edit</a>

        <!-- DELETE -->
        <a href="?id=<?= $id ?>&mode=delete" class="action-btn btn-delete">Delete</a>

    <?php endif; ?>

<?php elseif ($mode === 'edit'): ?>

    <!-- SUBMIT EDIT -->
    <button type="submit" name="action" value="edit" class="action-btn btn-submit">
        Submit
    </button>

<?php elseif ($mode === 'delete'): ?>

    <p style="color:red;font-weight:bold;">Confirm delete?</p>

    <button type="submit" name="action" value="delete" class="action-btn btn-delete">
        Confirm Delete
    </button>

<?php elseif ($mode === 'add'): ?>

    <button type="submit" name="action" value="add" class="action-btn btn-submit">
        Add
    </button>

<?php endif; ?>

</div>

</form>
</div>

<!-- ========================
     COPY SCRIPT
======================== -->
<script>
function copyPrompt(){
    const title = <?= json_encode($prompt['title'] ?? '') ?>;
    const content = <?= json_encode($prompt['content'] ?? '') ?>;

    const text = `PROMPT TITLE: ${title}\n\nPROMPT CONTENT: ${content}`;

    navigator.clipboard.writeText(text)
        .then(() => alert("Copied!"));
}
</script>

</body>
</html>