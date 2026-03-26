<?php

require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
$id = $_POST['id'] ?? null;
if(!$id) header("Location: ../index.php");
$stmt = $pdo->prepare("SELECT user_id FROM prompts WHERE id=?");
$stmt->execute([$id]);
$prompt = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prompt || !canEditPrompts($prompt['user_id'])){
    die("Access denied");
}

$del = $pdo->prepare("DELETE FROM prompts WHERE id=?");
$del->execute([$id]);
}
header("Location: ../index.php");
exit();