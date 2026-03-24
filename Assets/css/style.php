<?php
header("Content-Type: text/css");

require '../../auth/db.php';

/* COLORS */
$colors = [
    '#1abc9c','#9b59b6','#e67e22','#e74c3c',
    '#3498db','#f1c40f','#2ecc71','#34495e',
    '#d35400','#7f8c8d'
];

/* GET CATEGORY IDS */
$categories = $pdo->query("SELECT id FROM categories ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

/* GENERATE CLASSES */
foreach($categories as $i => $catId){
    $color = $colors[$i % count($colors)];
    echo ".category-badge-$catId { background: $color; }\n";
}