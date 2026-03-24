<?php
require 'auth/auth.php';
require 'auth/db.php';
require 'auth/role.php';

/* ---------- PARAMETERS ---------- */
$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort     = $_GET['sort'] ?? 'title';
$order    = $_GET['order'] ?? 'ASC';
$page     = $_GET['page'] ?? 1;

/* ---------- SORT SECURITY ---------- */
$allowedSort = ['title','status','created_at','category_name','developer'];
if (!in_array($sort,$allowedSort)) $sort = 'created_at';
$order = ($order === 'DESC') ? 'DESC' : 'ASC';

/* ---------- PAGINATION ---------- */
$limit  = 10;
$page   = max(1,(int)$page);
$offset = ($page - 1) * $limit;

/* ---------- QUERY ---------- */
$sql = "SELECT prompts.*, 
               categories.name AS category_name,
               users.username AS developer
        FROM prompts
        INNER JOIN categories ON prompts.category_id = categories.id
        INNER JOIN users ON prompts.user_id = users.id
        WHERE 1";

$params = [];

/* Search */
if ($search !== '') {
    $sql .= " AND (prompts.title LIKE :search OR prompts.content LIKE :search)";
    $params['search'] = "%$search%";
}

/* Category filter */
if ($category !== '') {
    $sql .= " AND prompts.category_id = :category";
    $params['category'] = $category;
}

/* SORT FIX (alias mapping) */
if($sort === 'developer'){
    $sort = 'users.username';
}
if($sort === 'category_name'){
    $sort = 'categories.name';
}

/* Sorting + Pagination */
$sql .= " ORDER BY $sort $order
          LIMIT :limit OFFSET :offset";

/* Execute query */
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(":$key", $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$prompts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- COUNT FOR PAGINATION & FILTERED prompts ---------- */
/* TOTAL */
$totalStmt = $pdo->query("SELECT COUNT(*) FROM prompts");
$totalPrompts = $totalStmt->fetchColumn();

/* FILTERED */
$filteredSql = "SELECT COUNT(*) FROM prompts WHERE 1";
$filteredParams = [];

if ($search !== '') {
    $filteredSql .= " AND (title LIKE :search OR content LIKE :search)";
    $filteredParams['search'] = "%$search%";
}

if ($category !== '') {
    $filteredSql .= " AND category_id = :category";
    $filteredParams['category'] = $category;
}

$filteredStmt = $pdo->prepare($filteredSql);
$filteredStmt->execute($filteredParams);
$filteredPrompts = $filteredStmt->fetchColumn();

/* USER */
$userStmt = $pdo->prepare("SELECT COUNT(*) FROM prompts WHERE user_id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$userPrompts = $userStmt->fetchColumn();

/* PAGINATION */
$totalPages = ceil($filteredPrompts / $limit);

/* ---------- LOAD CATEGORIES ---------- */
$categories = $pdo->query("SELECT * FROM categories")
                  ->fetchAll(PDO::FETCH_ASSOC);

/* ---------- BUILD BASE QUERY ---------- */
$queryBase = http_build_query([
    'search' => $search,
    'category' => $category,
    'page' => $page
]);

/* ---------- SORT LINK FUNCTION ---------- */
function sortLink($column, $label, $sort, $order, $queryBase) {
    $newOrder = ($column === $sort && $order === 'ASC') ? 'DESC' : 'ASC';
    $arrow = '';
    if ($column === $sort) {
        $arrow = $order === 'ASC' ? ' ↑' : ' ↓';
    }
    return "<a href='?$queryBase&sort=$column&order=$newOrder'>$label$arrow</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DevGenius Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style.php">
</head>
<body>
<h1>DevGenius: Prompt Repository Dashboard</h1>
<br><br>
<div class="p-welcome">
    Welcome <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span> (<span class="role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>) |
    <a href="logout.php" class="logout-btn">Logout</a> 
    <?php if(canManageUsers()): ?>
    <a href="admin/users.php" class="btn-manage">Manage Users</a>
    <a href="admin/categories.php" class="btn-manage">Manage Categories</a>
    <?php endif; ?>
</div> 
<br>
<div class="summary-container">
    <div class="summary-item">
        <h4>Total Prompts:</h4>
        <p><?= htmlspecialchars($totalPrompts) ?></p>
    </div>

    <div class="summary-item">
        <h4>Filtered results:</h4>
        <p><?= htmlspecialchars($filteredPrompts ?? 0) ?></p>
    </div>

    <div class="summary-item">
        <h4>Your Prompts:</h4>
        <p><?= htmlspecialchars($userPrompts ?? 0) ?></p>
    </div>
</div>
<br>
<!-- Toolbar -->
<div class="toolbar">
    <?php if(canCreatePrompt()): ?>
    <a href="devgest/add_prompt.php" class="btn-add">Add Prompt</a>
    <?php endif; ?>

    <form method="GET" class="filter-form">
        <input name="search" placeholder="Search prompt" value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($category==$c['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<!-- Table -->
<table>
    <tr>
        <th><?= sortLink('title','Title',$sort,$order,$queryBase) ?></th>
        <th>Content</th>
        <th><?= sortLink('status','Status',$sort,$order,$queryBase) ?></th>
        <th><?= sortLink('category_name','Category',$sort,$order,$queryBase) ?></th>
        <th><?= sortLink('developer','Developer',$sort,$order,$queryBase) ?></th>
        <th><?= sortLink('created_at','Date',$sort,$order,$queryBase) ?></th>
        <th>Actions</th>
    </tr>

    <?php foreach ($prompts as $p): 
    $statusClass = strtolower(str_replace(' ','-',$p['status']));
    ?>

    <tr>
    <td><?= htmlspecialchars($p['title']) ?></td>

    <td><?= htmlspecialchars(substr($p['content'],0,80)) ?>...</td>

    <td>
    <span class="status-badge status-<?= $statusClass ?>">
    <?= $p['status'] ?>
    </span>
    </td>

    <td>
    <span class="status-badge category-badge-<?= $p['category_id'] ?>">
    <?= htmlspecialchars($p['category_name']) ?>
    </span>
    </td>

    <td><?= htmlspecialchars($p['developer']) ?></td>

    <td><?= $p['created_at'] ?></td>

    <td class="actions">

    <?php if(canEditPrompts($p['user_id'])): ?>
    <a href="devgest/update_prompt.php?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
    <a href="devgest/delete_prompt.php?id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Delete this prompt?')">Delete</a>
    <?php endif; ?>

    </td>
    </tr>

    <?php endforeach; ?>
</table>

<!-- Pagination -->
<div class="pagination">
<?php for ($i=1;$i<=$totalPages;$i++): ?>
<a href="?search=<?= $search ?>&category=<?= $category ?>&sort=<?= $_GET['sort'] ?? 'title' ?>&order=<?= $order ?>&page=<?= $i ?>"
<?= ($i==$page)?'class="active"':'' ?>>
<?= $i ?>
</a>
<?php endfor; ?>
</div>

</body>
</html>