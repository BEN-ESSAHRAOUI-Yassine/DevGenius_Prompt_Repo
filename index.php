<?php
require 'auth/auth.php';
require 'auth/db.php';
require 'auth/role.php';

/* ---------- PARAMETERS ---------- */
$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort     = $_GET['sort'] ?? 'device_name';
$order    = $_GET['order'] ?? 'ASC';
$page     = $_GET['page'] ?? 1;

/* ---------- SORT SECURITY ---------- */
$allowedSort = ['device_name','price','status','serial_number'];
if (!in_array($sort,$allowedSort)) $sort = 'device_name';
$order = ($order === 'DESC') ? 'DESC' : 'ASC';

/* ---------- PAGINATION ---------- */
$limit  = 10;
$page   = max(1,(int)$page);
$offset = ($page - 1) * $limit;

/* ---------- QUERY ---------- */
$sql = "SELECT assets.*, categories.name AS category_name, assets.user_id 
        FROM assets 
        INNER JOIN categories ON assets.category_id = categories.id 
        WHERE 1";

$params = [];

/* Search */
if ($search !== '') {
    $sql .= " AND (device_name LIKE :search OR serial_number LIKE :search)";
    $params['search'] = "%$search%";
}

/* Category filter */
if ($category !== '') {
    $sql .= " AND category_id = :category";
    $params['category'] = $category;
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

$assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- COUNT FOR PAGINATION & FILTERED ASSETS ---------- */
$countSql = "SELECT COUNT(*) FROM assets WHERE 1";
$countParams = [];

if ($search !== '') {
    $countSql .= " AND (device_name LIKE :search OR serial_number LIKE :search)";
    $countParams['search'] = "%$search%";
}
if ($category !== '') {
    $countSql .= " AND category_id = :category";
    $countParams['category'] = $category;
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);
$totalAssets = $countStmt->fetchColumn(); // filtered asset count
$totalPages  = ceil($totalAssets / $limit);

/* ---------- TOTAL INVENTORY VALUE ---------- */
$valueStmt = $pdo->query("SELECT SUM(price) FROM assets");
$totalValue = $valueStmt->fetchColumn();

/* ---------- FILTERED INVENTORY VALUE ---------- */
$filteredValueSql = "SELECT SUM(price) FROM assets WHERE 1";
$filteredParams = [];

if ($search !== '') {
    $filteredValueSql .= " AND (device_name LIKE :search OR serial_number LIKE :search)";
    $filteredParams['search'] = "%$search%";
}
if ($category !== '') {
    $filteredValueSql .= " AND category_id = :category";
    $filteredParams['category'] = $category;
}

$filteredStmt = $pdo->prepare($filteredValueSql);
$filteredStmt->execute($filteredParams);
$filteredValue = $filteredStmt->fetchColumn();

/* ---------- TOTAL ASSET COUNT ---------- */
$totalAssetsStmt = $pdo->query("SELECT COUNT(*) FROM assets");
$totalAssetsInventory = $totalAssetsStmt->fetchColumn();

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
    $url = "?$queryBase&sort=$column&order=$newOrder";
    return "<a href='$url'>$label$arrow</a>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GearLog Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.php">
</head>
<body>

<h1>GearLog - Asset Dashboard</h1>

<div class="p-welcome">
    Welcome <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span> (<span class="role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>) |
   
    <a href="logout.php" class="logout-btn">Logout</a> 
    <?php if(canManageUsers()): ?>
    <a href="admin/users.php" class="btn-manage">Manage Users</a>
    <?php endif; ?>
</div> 
<br>
<div class="summary-container">
    <div class="summary-item">
        <h4>Total Inventory Value:</h4>
        <p>$<?= htmlspecialchars($totalValue) ?></p>
    </div>

    <div class="summary-item">
        <h4>Filtered Inventory Value:</h4>
        <p>$<?= htmlspecialchars($filteredValue ?? 0) ?></p>
    </div>

    <div class="summary-item">
        <h4>Shown Assets:</h4>
        <p><?= htmlspecialchars($totalAssets) ?> / <?= htmlspecialchars($totalAssetsInventory) ?></p>
    </div>
</div>
<br>
<!-- Toolbar -->
<div class="toolbar">
    <?php if(canEditPrompts()): ?>
        <a href="add_asset.php" class="btn-add">Add New Asset</a>
    <?php endif; ?>

    <form method="GET" class="filter-form">
        <input name="search" placeholder="Search asset" value="<?= htmlspecialchars($search) ?>">
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
    <th><?= sortLink('device_name','Device',$sort,$order,$queryBase) ?></th>
    <th><?= sortLink('serial_number','Serial',$sort,$order,$queryBase) ?></th>
    <th><?= sortLink('price','Price',$sort,$order,$queryBase) ?></th>
    <th><?= sortLink('status','Status',$sort,$order,$queryBase) ?></th>
    <th>Category</th>
    <th>Actions</th>
</tr>

<?php foreach ($assets as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['device_name']) ?></td>
    <td><?= htmlspecialchars($a['serial_number']) ?></td>
    <td>$<?= htmlspecialchars($a['price']) ?></td>

    <?php $statusClass = strtolower(str_replace(' ','-',$a['status'])); ?>
    <td><span class="status-badge status-<?= $statusClass ?>"><?= htmlspecialchars($a['status']) ?></span></td>

    <td><?= htmlspecialchars($a['category_name']) ?></td>
    <td class="actions">

        <?php if(canEditPrompts()): ?>

        <a href="update_asset.php?id=<?= $a['id'] ?>" class="btn-edit">Edit</a>

        <a href="delete_asset.php?id=<?= $a['id'] ?>" 
        class="btn-delete"
        onclick="return confirm('Delete this asset?')">
        Delete
        </a>

        <?php else: ?>

        <span style="color:gray">Read Only</span>

        <?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>
</table>

<!-- Pagination -->
<div class="pagination">
<?php for ($i=1;$i<=$totalPages;$i++): ?>
    <a href="?search=<?= $search ?>&category=<?= $category ?>&sort=<?= $sort ?>&order=<?= $order ?>&page=<?= $i ?>"><?= $i ?></a>
<?php endfor; ?>
</div>

</body>
</html>