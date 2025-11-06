<?php
require '../auth_check.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}

$module_id = (int)$_GET['id'];

// Récupérer le course_id avant de supprimer
$conn = db_connect();
$stmt = $conn->prepare('SELECT course_id FROM modules WHERE id = ?');
$stmt->bind_param('i', $module_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../courses/index.php');
    exit();
}

$module = $result->fetch_assoc();
$course_id = $module['course_id'];
$stmt->close();

// Supprimer le module (les leçons seront supprimées automatiquement grâce à ON DELETE CASCADE)
$stmt = $conn->prepare('DELETE FROM modules WHERE id = ?');
$stmt->bind_param('i', $module_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: ../courses/content.php?course_id=' . $course_id . '&success=module_deleted');
exit();
