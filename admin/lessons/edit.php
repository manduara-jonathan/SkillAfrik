<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$lesson_id = (int)$_GET['id'];

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token()) {
        $title = trim($_POST['title']);
        $content_type = $_POST['content_type'];
        $content = trim($_POST['content']);

        $stmt = $conn->prepare('UPDATE lessons SET title = ?, content_type = ?, content = ? WHERE id = ?');
        $stmt->bind_param('sssi', $title, $content_type, $content, $lesson_id);
        $stmt->execute();
        $course_id = $conn->query('SELECT m.course_id FROM modules m JOIN lessons l ON m.id = l.module_id WHERE l.id = ' . $lesson_id)->fetch_assoc()['course_id'];
        $stmt->close();
        
        header('Location: ../courses/content.php?course_id=' . $course_id);
        exit();
    }
}

$stmt = $conn->prepare('SELECT title, content_type, content FROM lessons WHERE id = ?');
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$lesson) {
    header('Location: ../courses/index.php');
    exit();
}

$pageTitle = "Modifier la leçon";
include '../partials/header.php';
?>

<div class="container">
    <h1>Modifier la leçon</h1>
    <form action="" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Titre de la leçon</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="content_type">Type de contenu</label>
            <select id="content_type" name="content_type">
                <option value="text" <?php if($lesson['content_type'] == 'text') echo 'selected'; ?>>Texte</option>
                <option value="video" <?php if($lesson['content_type'] == 'video') echo 'selected'; ?>>Vidéo (URL YouTube)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="content">Contenu</label>
            <textarea id="content" name="content" rows="10"><?php echo htmlspecialchars($lesson['content']); ?></textarea>
        </div>
        <?php csrf_input(); ?>
        <button type="submit" class="btn-primary">Mettre à jour</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
