<?php
require '../auth_check.php';
require '../../config/csrf.php';
require '../../config/database.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: index.php');
    exit();
}
$course_id = (int)$_GET['id'];

$conn = db_connect();

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        die('Invalide CSRF token');
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    $stmt = $conn->prepare('UPDATE courses SET title = ?, description = ?, image_url = ? WHERE id = ?');
    $stmt->bind_param('sssi', $title, $description, $image_url, $course_id);
    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    }
    $stmt->close();
}

// Récupérer les données actuelles du cours
$stmt = $conn->prepare('SELECT title, description, image_url FROM courses WHERE id = ?');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    header('Location: index.php');
    exit();
}

$conn->close();

$pageTitle = "Modifier le cours";
include '../partials/header.php';
?>

<div class="container">
    <h1>Modifier le cours</h1>

    <form action="edit.php?id=<?php echo $course_id; ?>" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Titre du cours</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($course['title']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="8"><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image_url">URL de l'image</label>
            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($course['image_url']); ?>">
        </div>
        <?php csrf_input(); ?>
        <button type="submit" class="btn-primary">Mettre à jour</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
