<?php
require 'config/csrf.php';
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !validate_csrf_token()) {
    die('Accès non autorisé');
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$lesson_id = (int)$_POST['lesson_id'];
$user_answers = $_POST['answers'];

$conn = db_connect();

// Récupérer le quiz_id et les bonnes réponses
$quiz_id = $conn->query('SELECT id FROM quizzes WHERE lesson_id = ' . $lesson_id)->fetch_assoc()['id'];
$correct_answers_result = $conn->query('SELECT q.id as question_id, a.id as answer_id FROM questions q JOIN answers a ON q.id = a.question_id WHERE q.quiz_id = ' . $quiz_id . ' AND a.is_correct = 1');
$correct_answers = [];
while ($row = $correct_answers_result->fetch_assoc()) {
    $correct_answers[$row['question_id']] = $row['answer_id'];
}

// Calculer le score
$score = 0;
$total_questions = count($correct_answers);
foreach ($user_answers as $question_id => $answer_id) {
    if (isset($correct_answers[$question_id]) && $correct_answers[$question_id] == $answer_id) {
        $score++;
    }
}

$final_score = ($total_questions > 0) ? ($score / $total_questions) * 100 : 0;

// Enregistrer la tentative
$stmt = $conn->prepare('INSERT INTO quiz_attempts (user_id, quiz_id, score) VALUES (?, ?, ?)');
$stmt->bind_param('iid', $user_id, $quiz_id, $final_score);
$stmt->execute();
$attempt_id = $stmt->insert_id;
$stmt->close();

// Marquer la leçon comme terminée
$stmt = $conn->prepare('INSERT IGNORE INTO lesson_completions (user_id, lesson_id) VALUES (?, ?)');
$stmt->bind_param('ii', $user_id, $lesson_id);
$stmt->execute();
$stmt->close();

$conn->close();

header('Location: quiz_results.php?attempt_id=' . $attempt_id);
exit();
