<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['course_id']) || !filter_var($_GET['course_id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$course_id = (int)$_GET['course_id'];

// Vérifier que le cours existe
$conn = db_connect();
$stmt = $conn->prepare('SELECT title FROM courses WHERE id = ?');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: ../courses/index.php');
    exit();
}
$course = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token()) {
        $title = trim($_POST['title']);
        $order = isset($_POST['order']) ? (int)$_POST['order'] : 0;

        if (!empty($title)) {
            $stmt = $conn->prepare('INSERT INTO modules (course_id, title, `order`) VALUES (?, ?, ?)');
            $stmt->bind_param('isi', $course_id, $title, $order);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: ../courses/content.php?course_id=' . $course_id . '&success=module_created');
                exit();
            } else {
                $error = "Erreur lors de la création du module.";
            }
            $stmt->close();
        } else {
            $error = "Le titre du module est requis.";
        }
    } else {
        $error = "Token CSRF invalide.";
    }
}

$conn->close();

$pageTitle = "Créer un module";
include '../partials/header.php';
?>

<div class="container">
    <h1>Créer un nouveau module</h1>
    <p class="breadcrumb">
        <a href="../courses/index.php">Cours</a> &raquo; 
        <a href="../courses/content.php?course_id=<?php echo $course_id; ?>"><?php echo htmlspecialchars($course['title']); ?></a> &raquo; 
        Nouveau module
    </p>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Titre du module *</label>
            <input type="text" id="title" name="title" required 
                   placeholder="Ex: Introduction à PHP">
        </div>

        <div class="form-group">
            <label for="order">Ordre d'affichage</label>
            <input type="number" id="order" name="order" value="0" min="0"
                   placeholder="0 = premier">
            <small>Les modules seront affichés dans l'ordre croissant</small>
        </div>

        <?php csrf_input(); ?>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Créer le module</button>
            <a href="../courses/content.php?course_id=<?php echo $course_id; ?>" class="btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
