<?php
require 'auth_check.php';
require '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['type']) || !isset($data['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
    exit();
}

$type = $data['type'];
$items = $data['items'];

$conn = db_connect();

if ($type === 'module') {
    $stmt = $conn->prepare('UPDATE modules SET `order` = ? WHERE id = ?');
} elseif ($type === 'lesson') {
    $stmt = $conn->prepare('UPDATE lessons SET `order` = ? WHERE id = ?');
} else {
    echo json_encode(['status' => 'error', 'message' => 'Type invalide']);
    exit();
}

foreach ($items as $item) {
    $stmt->bind_param('ii', $item['order'], $item['id']);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success']);
