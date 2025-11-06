<?php
require 'config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['attempt_id'])) {
    header('Location: dashboard.php');
    exit();
}

$attempt_id = (int)$_GET['attempt_id'];
$user_id = $_SESSION['user_id'];

$conn = db_connect();

// Vérifier que la tentative appartient bien à l'utilisateur connecté
$stmt = $conn->prepare('SELECT qa.score, q.title, l.id as lesson_id, m.course_id
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    JOIN lessons l ON q.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    WHERE qa.id = ? AND qa.user_id = ?');
$stmt->bind_param('ii', $attempt_id, $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$result) {
    header('Location: dashboard.php');
    exit();
}

$pageTitle = "Résultats du Quiz";
include 'views/partials/header.php';
?>

<div class="container result-container">
    <h1>Résultats pour : <?php echo htmlspecialchars($result['title']); ?></h1>
    <div class="score-display">
        <p>Votre score</p>
        <span><?php echo round($result['score']); ?>%</span>
    </div>
    <div class="result-actions">
        <a href="lesson.php?id=<?php echo $result['lesson_id']; ?>" class="btn-secondary">Revoir le quiz</a>
        <a href="course.php?id=<?php echo $result['course_id']; ?>" class="btn-primary">Retour au cours</a>
    </div>
</div>

<?php include 'views/partials/footer.php'; ?>
