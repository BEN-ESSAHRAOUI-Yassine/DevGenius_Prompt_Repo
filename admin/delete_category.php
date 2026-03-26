<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()) die("Access denied");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
$id = $_POST['id'] ?? null;
if(!$id || $id == 0) {
    header("Location: categories.php");
    exit;
}
    $stmt = $pdo->prepare("UPDATE prompts SET category_id = 0 WHERE category_id = ?");
    $stmt->execute([$id]);
    $del = $pdo->prepare("DELETE FROM categories WHERE id=?"); //DELETE FROM categories WHERE id=?
    $del->execute([$id]);
}
header("Location: categories.php");
exit();