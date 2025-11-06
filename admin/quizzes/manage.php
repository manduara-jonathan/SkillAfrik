<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['quiz_id']) || !filter_var($_GET['quiz_id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$quiz_id = (int)$_GET['quiz_id'];

$conn = db_connect();

// Gérer l'ajout d'une question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    if (validate_csrf_token()) {
        $question_text = trim($_POST['question_text']);
        $answers = $_POST['answers']; // Tableau de réponses
        $correct_answer_index = $_POST['is_correct'];

        // Insérer la question
        $stmt = $conn->prepare('INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)');
        $stmt->bind_param('is', $quiz_id, $question_text);
        $stmt->execute();
        $question_id = $stmt->insert_id;
        $stmt->close();

        // Insérer les réponses
        $stmt = $conn->prepare('INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)');
        foreach ($answers as $index => $answer_text) {
            if (!empty($answer_text)) {
                $is_correct = ($index == $correct_answer_index) ? 1 : 0;
                $stmt->bind_param('isi', $question_id, $answer_text, $is_correct);
                $stmt->execute();
            }
        }
        $stmt->close();
        header('Location: manage_quiz.php?quiz_id=' . $quiz_id);
        exit();
    }
}

// Récupérer les infos du quiz et les questions
$quiz = $conn->query('SELECT * FROM quizzes WHERE id = ' . $quiz_id)->fetch_assoc();
$questions_result = $conn->query('SELECT * FROM questions WHERE quiz_id = ' . $quiz_id);
$questions = [];
while ($question = $questions_result->fetch_assoc()) {
    $question['answers'] = $conn->query('SELECT * FROM answers WHERE question_id = ' . $question['id'])->fetch_all(MYSQLI_ASSOC);
    $questions[] = $question;
}

// Récupérer le course_id avant de fermer la connexion
$course_id = $conn->query('SELECT m.course_id FROM modules m JOIN lessons l ON m.id = l.module_id WHERE l.id = ' . $quiz['lesson_id'])->fetch_assoc()['course_id'];
$conn->close();

$pageTitle = "Gérer le Quiz";
include '../partials/header.php';
?>

<div class="container">
    <a href="../courses/content.php?course_id=<?php echo $course_id; ?>">&larr; Retour au contenu</a>
    <h1>Gestion du Quiz : <?php echo htmlspecialchars($quiz['title']); ?></h1>

    <!-- Affichage des questions existantes -->
    <div class="questions-list">
        <h3>Questions existantes</h3>
        <?php foreach ($questions as $q): ?>
            <div class="question-item">
                <strong><?php echo htmlspecialchars($q['question_text']); ?></strong>
                <ul>
                    <?php foreach ($q['answers'] as $a): ?>
                        <li class="<?php echo $a['is_correct'] ? 'correct-answer' : ''; ?>"><?php echo htmlspecialchars($a['answer_text']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulaire pour ajouter une question -->
    <div class="admin-form-container">
        <h3>Ajouter une question (QCM)</h3>
        <form action="" method="POST">
            <?php csrf_input(); ?>
            <div class="form-group">
                <label for="question_text">Texte de la question</label>
                <input type="text" name="question_text" required>
            </div>
            <div class="form-group">
                <label>Réponses</label>
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="answer-option">
                    <input type="radio" name="is_correct" value="<?php echo $i; ?>" <?php if ($i==0) echo 'checked'; ?>>
                    <input type="text" name="answers[]" placeholder="Réponse <?php echo $i + 1; ?>">
                </div>
                <?php endfor; ?>
            </div>
            <button type="submit" name="add_question" class="btn-primary">Ajouter la question</button>
        </form>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
