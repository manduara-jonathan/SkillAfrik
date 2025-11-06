<?php
require 'config/csrf.php';
require 'config/database.php';

// 1. Récupérer l'ID du cours depuis l'URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // Rediriger si l'ID est manquant ou invalide
    header('Location: courses.php');
    exit();
}
$course_id = (int)$_GET['id'];

$conn = db_connect();

// 2. Gérer l'inscription si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    if (!validate_csrf_token()) {
        die('Invalide CSRF token');
    }

    if (!isset($_SESSION['user_id'])) {
        // L'utilisateur doit être connecté pour s'inscrire
        header('Location: login.php');
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Insérer dans la table enrollments
    $stmt = $conn->prepare('INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $user_id, $course_id);
    if ($stmt->execute()) {
        // Inscription réussie, recharger la page pour mettre à jour l'affichage
        header('Location: course.php?id=' . $course_id);
        exit();
    } else {
        // Gérer l'erreur (ex: déjà inscrit, ce qui devrait être empêché par la clé unique)
        $error_message = "Une erreur est survenue lors de l'inscription.";
    }
    $stmt->close();
}

// 3. Récupérer les détails du cours
$stmt = $conn->prepare('SELECT title, description, image_url FROM courses WHERE id = ?');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    // Si le cours n'existe pas, rediriger
    header('Location: courses.php');
    exit();
}

// 4. Vérifier si l'utilisateur est déjà inscrit
$is_enrolled = false;
$is_course_completed = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?');
    $stmt->bind_param('ii', $user_id, $course_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $is_enrolled = true;

        // Vérifier si le cours est terminé
        $progress_stmt = $conn->prepare('SELECT 
            (SELECT COUNT(*) FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = ?) as total_lessons,
            (SELECT COUNT(*) FROM lesson_completions lc WHERE lc.user_id = ? AND lc.lesson_id IN (SELECT l.id FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = ?)) as completed_lessons
        ');
        $progress_stmt->bind_param('iii', $course_id, $user_id, $course_id);
        $progress_stmt->execute();
        $progress_data = $progress_stmt->get_result()->fetch_assoc();
        $progress_stmt->close();

        if ($progress_data['total_lessons'] > 0 && $progress_data['completed_lessons'] >= $progress_data['total_lessons']) {
            $is_course_completed = true;
        }
    }
    $stmt->close();
}

// 5. Récupérer les modules du cours avec l'ID de leur première leçon
$stmt = $conn->prepare('
    SELECT m.title, (SELECT l.id FROM lessons l WHERE l.module_id = m.id ORDER BY l.order ASC LIMIT 1) as first_lesson_id
    FROM modules m
    WHERE m.course_id = ?
    ORDER BY m.order ASC
');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$modules = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

$pageTitle = htmlspecialchars($course['title']) . " - SkillAfrik";
include 'views/partials/header.php';
include 'views/course.php';
include 'views/partials/footer.php';
