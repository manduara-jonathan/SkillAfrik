<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['module_id']) || !filter_var($_GET['module_id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$module_id = (int)$_GET['module_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token()) {
        $title = trim($_POST['title']);
        $content_type = $_POST['content_type'];
        $content = trim($_POST['content']);

        $conn = db_connect();
        
        // Insérer la leçon
        $stmt = $conn->prepare('INSERT INTO lessons (module_id, title, content_type, content) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $module_id, $title, $content_type, $content);
        $stmt->execute();
        $lesson_id = $stmt->insert_id;
        $stmt->close();

        $course_id = $conn->query('SELECT course_id FROM modules WHERE id = ' . $module_id)->fetch_assoc()['course_id'];

        // Si c'est un quiz, créer le quiz associé et rediriger vers sa gestion
        if ($content_type === 'quiz') {
            $quiz_stmt = $conn->prepare('INSERT INTO quizzes (lesson_id, title) VALUES (?, ?)');
            $quiz_stmt->bind_param('is', $lesson_id, $title);
            $quiz_stmt->execute();
            $quiz_id = $quiz_stmt->insert_id;
            $quiz_stmt->close();
            $conn->close();
            header('Location: ../quizzes/manage.php?quiz_id=' . $quiz_id);
            exit();
        }

        $conn->close();
        header('Location: ../courses/content.php?course_id=' . $course_id);
        exit();
    }
}

$pageTitle = "Créer une leçon";
include '../partials/header.php';
?>

<div class="container">
    <h1>Créer une nouvelle leçon</h1>
    <form action="" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Titre de la leçon</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="content_type">Type de contenu</label>
            <select id="content_type" name="content_type">
                <option value="text">Texte</option>
                <option value="video">Vidéo (URL YouTube)</option>
                <option value="quiz">Quiz</option>
            </select>
        </div>
        <div class="form-group">
            <label for="content">Contenu</label>
            <textarea id="content" name="content" rows="10" placeholder="Entrez votre texte ou le lien de la vidéo YouTube."></textarea>
        </div>
        <?php csrf_input(); ?>
        <button type="submit" class="btn-primary">Enregistrer la leçon</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
