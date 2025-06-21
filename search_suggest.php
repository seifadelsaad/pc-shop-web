<?php
include 'config.php';
header('Content-Type: application/json');
$q = trim($_GET['q'] ?? '');
if (!$q) { echo json_encode([]); exit; }
$safe = $conn->real_escape_string($q);
$res = $conn->query("SELECT id, name FROM products WHERE name LIKE '%$safe%' OR description LIKE '%$safe%' ORDER BY name LIMIT 8");
$suggestions = [];
while ($row = $res->fetch_assoc()) {
    $suggestions[] = $row;
}
echo json_encode($suggestions); 