<?php
require '../auth_check.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: index.php');
    exit();
}
$course_id = (int)$_GET['id'];

$conn = db_connect();

// Pour un vrai projet, il faudrait supprimer en cascade les modules, leçons, inscriptions, etc.
// Ici, nous faisons une suppression simple pour l'exemple.

$stmt = $conn->prepare('DELETE FROM courses WHERE id = ?');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
exit();
