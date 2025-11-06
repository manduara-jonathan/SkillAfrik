<?php
session_start();
require 'config/database.php';
require 'config/csrf.php';

// 1. Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// 2. Récupérer l'ID de la leçon et valider
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: dashboard.php');
    exit();
}
$lesson_id = (int)$_GET['id'];

$conn = db_connect();

// 3. Récupérer les détails de la leçon et du cours associé
$stmt = $conn->prepare('SELECT l.id, l.title, l.content_type, l.content, m.course_id, c.title as course_title
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    JOIN courses c ON m.course_id = c.id
    WHERE l.id = ?');
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc();
$stmt->close();

if (!$lesson) {
    // Leçon non trouvée
    header('Location: dashboard.php');
    exit();
}

$course_id = $lesson['course_id'];

// 4. Vérifier si l'utilisateur est bien inscrit à ce cours
$stmt = $conn->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?');
$stmt->bind_param('ii', $user_id, $course_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    // Non inscrit, accès refusé
    header('Location: course.php?id=' . $course_id);
    exit();
}
$stmt->close();

// 5. Récupérer toutes les leçons du cours pour la navigation
$stmt = $conn->prepare('SELECT m.title as module_title, l.id as lesson_id, l.title as lesson_title
    FROM modules m
    JOIN lessons l ON m.id = l.module_id
    WHERE m.course_id = ?
    ORDER BY m.order, l.order');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$course_lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 6. Vérifier si la leçon est déjà complétée
$is_completed = false;
$stmt = $conn->prepare('SELECT id FROM lesson_completions WHERE user_id = ? AND lesson_id = ?');
$stmt->bind_param('ii', $user_id, $lesson_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $is_completed = true;
}
$stmt->close();

// Si c'est un quiz, récupérer les questions et réponses
$quiz_data = [];
if ($lesson['content_type'] === 'quiz') {
    $stmt = $conn->prepare('SELECT q.id as question_id, q.question_text, a.id as answer_id, a.answer_text
        FROM quizzes qz
        JOIN questions q ON qz.id = q.quiz_id
        JOIN answers a ON q.id = a.question_id
        WHERE qz.lesson_id = ?');
    $stmt->bind_param('i', $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $quiz_data[$row['question_id']]['question_text'] = $row['question_text'];
        $quiz_data[$row['question_id']]['answers'][] = ['id' => $row['answer_id'], 'text' => $row['answer_text']];
    }
    $stmt->close();
}

$conn->close();

$pageTitle = htmlspecialchars($lesson['title']) . " - SkillAfrik";
include 'views/partials/header.php';
include 'views/lesson.php';
include 'views/partials/footer.php';
