<?php
require '../auth_check.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$lesson_id = (int)$_GET['id'];

$conn = db_connect();

// Récupérer l'ID du cours pour la redirection
$course_id = $conn->query('SELECT m.course_id FROM modules m JOIN lessons l ON m.id = l.module_id WHERE l.id = ' . $lesson_id)->fetch_assoc()['course_id'];

$stmt = $conn->prepare('DELETE FROM lessons WHERE id = ?');
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: ../courses/content.php?course_id=' . $course_id);
exit();
