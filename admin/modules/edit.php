<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../courses/index.php');
    exit();
}
$module_id = (int)$_GET['id'];

// Récupérer les informations du module
$conn = db_connect();
$stmt = $conn->prepare('SELECT m.*, c.title as course_title, c.id as course_id 
                        FROM modules m 
                        JOIN courses c ON m.course_id = c.id 
                        WHERE m.id = ?');
$stmt->bind_param('i', $module_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ../courses/index.php');
    exit();
}

$module = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (validate_csrf_token()) {
        $title = trim($_POST['title']);
        $order = isset($_POST['order']) ? (int)$_POST['order'] : 0;

        if (!empty($title)) {
            $stmt = $conn->prepare('UPDATE modules SET title = ?, `order` = ? WHERE id = ?');
            $stmt->bind_param('sii', $title, $order, $module_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: ../courses/content.php?course_id=' . $module['course_id'] . '&success=module_updated');
                exit();
            } else {
                $error = "Erreur lors de la modification du module.";
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

$pageTitle = "Modifier le module";
include '../partials/header.php';
?>

<div class="container">
    <h1>Modifier le module</h1>
    <p class="breadcrumb">
        <a href="../courses/index.php">Cours</a> &raquo; 
        <a href="../courses/content.php?course_id=<?php echo $module['course_id']; ?>"><?php echo htmlspecialchars($module['course_title']); ?></a> &raquo; 
        Modifier module
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
                   value="<?php echo htmlspecialchars($module['title']); ?>">
        </div>

        <div class="form-group">
            <label for="order">Ordre d'affichage</label>
            <input type="number" id="order" name="order" 
                   value="<?php echo htmlspecialchars($module['order']); ?>" min="0">
            <small>Les modules seront affichés dans l'ordre croissant</small>
        </div>

        <?php csrf_input(); ?>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            <a href="../courses/content.php?course_id=<?php echo $module['course_id']; ?>" class="btn-secondary">Annuler</a>
        </div>
    </form>

    <div class="danger-zone">
        <h3>Zone de danger</h3>
        <p>La suppression d'un module supprimera également toutes ses leçons.</p>
        <a href="delete.php?id=<?php echo $module_id; ?>" 
           class="btn-danger" 
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce module et toutes ses leçons ?');">
            Supprimer ce module
        </a>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
