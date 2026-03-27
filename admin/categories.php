<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!isAdmin()){
    die("Access denied");
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Prepare data: total prompts & most active Developper for each category
$categoryData = [];
foreach($categories as $cat){
    // Total prompts in category
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM prompts WHERE category_id = ?");
    $stmt->execute([$cat['id']]);
    $totalPrompts = $stmt->fetchColumn();
    // Most active Developper in category
    $stmt = $pdo->prepare("
        SELECT user_id, COUNT(*) AS cnt, u.username 
        FROM prompts p
        INNER JOIN users u ON p.user_id = u.id
        WHERE category_id = ?
        GROUP BY user_id
        ORDER BY cnt DESC
        LIMIT 1
    ");
    $stmt->execute([$cat['id']]);
    $mostActive = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $categoryData[] = [
        'id' => $cat['id'],
        'name' => $cat['name'],
        'total_prompts' => $totalPrompts,
        'most_active_dev' => $mostActive ? $mostActive['username'] : '-',
        'most_active_count' => $mostActive ? $mostActive['cnt'] : 0
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style.php">
</head>
<body>
<h1>Categories Overview</h1>
<a href="../index.php" class="action-btn btn-back">← Back to Dashboard</a>
<a href="add_category.php" class="action-btn btn-add">Add Category</a>
<br><br>


<div>
    <table>
    <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Total Prompts</th>
        <th>Most Active Developper</th>
        <th>Prompts by Developper</th>
        <th>Actions</th>
    </tr>

    <?php foreach($categoryData as $c): ?>
    <tr>
        <td><?= $c['id'] ?></td>
        <td><?= htmlspecialchars($c['name']) ?></td>
        <td><?= $c['total_prompts'] ?></td>
        <td><?= htmlspecialchars($c['most_active_dev']) ?></td>
        <td><?= htmlspecialchars($c['most_active_count']) ?></td>
        <td class="actions">
            <a href="edit_category.php?id=<?= $c['id'] ?>" class="action-btn btn-edit">Edit</a>
            <?php if($c['id'] != 0): ?>
            <form method="POST" action="delete_category.php" 
                onsubmit="return confirm('Delete this category?')" 
                style="display:inline;">

                <input type="hidden" name="id" value="<?= $c['id'] ?>">

                <button type="submit" class="action-btn btn-delete">Delete</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
</body>
</html>