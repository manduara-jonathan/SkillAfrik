<?php
session_start();
require_once 'config/database.php';

// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Tableau de bord - SkillAfrik";

$conn = db_connect();
$user_id = $_SESSION['user_id'];

// Récupérer les cours de l'utilisateur et calculer la progression
$stmt = $conn->prepare('SELECT 
    c.id, c.title, c.image_url,
    (SELECT COUNT(*) FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = c.id) as total_lessons,
    (SELECT COUNT(*) FROM lesson_completions lc WHERE lc.user_id = ? AND lc.lesson_id IN (SELECT l.id FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = c.id)) as completed_lessons
    FROM courses c
    JOIN enrollments e ON c.id = e.course_id
    WHERE e.user_id = ?');
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courses_data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$courses = [];
foreach ($courses_data as $course_data) {
    $progress = 0;
    if ($course_data['total_lessons'] > 0) {
        $progress = round(($course_data['completed_lessons'] / $course_data['total_lessons']) * 100);
    }
    $courses[] = [
        'id' => $course_data['id'],
        'title' => $course_data['title'],
        'image' => $course_data['image_url'],
        'progress' => $progress
    ];
}

// Données factices pour les éléments récents (à développer plus tard)
$recent_items = [];

$conn->close();

include 'views/partials/header.php';
include 'views/dashboard.php';
include 'views/partials/footer.php';
