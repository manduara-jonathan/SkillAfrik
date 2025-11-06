<?php
require '../auth_check.php';
require '../../config/csrf.php';
require '../../config/database.php';

$errors = [];
$title = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        die('Invalide CSRF token');
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/';
        $file_name = uniqid() . '-' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'uploads/' . $file_name;
        } else {
            $errors[] = "Erreur lors de l'upload de l'image.";
        }
    }
    $created_by = $_SESSION['user_id'];

    if (empty($title)) {
        $errors[] = 'Le titre est requis.';
    }
    if (empty($description)) {
        $errors[] = 'La description est requise.';
    }

    if (empty($errors)) {
        $conn = db_connect();
        $stmt = $conn->prepare('INSERT INTO courses (title, description, image_url, created_by) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $title, $description, $image_url, $created_by);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit();
        } else {
            $errors[] = 'Erreur lors de la création du cours.';
        }
        $stmt->close();
        $conn->close();
    }
}

$pageTitle = "Créer un cours";
include '../partials/header.php';
?>

<div class="container">
    <h1>Créer un nouveau cours</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="create_course.php" method="POST" class="admin-form" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Titre du cours</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="8"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image du cours</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <?php csrf_input(); ?>
        <button type="submit" class="btn-primary">Enregistrer le cours</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
