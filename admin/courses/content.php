<?php
require '../auth_check.php';
require '../../config/database.php';
require '../../config/csrf.php';

if (!isset($_GET['course_id']) || !filter_var($_GET['course_id'], FILTER_VALIDATE_INT)) {
    header('Location: index.php');
    exit();
}
$course_id = (int)$_GET['course_id'];

$conn = db_connect();

// Gérer l'ajout d'un nouveau module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_module'])) {
    if (validate_csrf_token()) {
        $module_title = trim($_POST['module_title']);
        if (!empty($module_title)) {
            $stmt = $conn->prepare('INSERT INTO modules (course_id, title) VALUES (?, ?)');
            $stmt->bind_param('is', $course_id, $module_title);
            $stmt->execute();
            $stmt->close();
            header('Location: manage_content.php?course_id=' . $course_id); // Recharger pour voir le nouveau module
            exit();
        }
    }
}

// Récupérer les détails du cours
$stmt = $conn->prepare('SELECT title FROM courses WHERE id = ?');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Récupérer les modules et leurs leçons
$stmt = $conn->prepare('SELECT id, title FROM modules WHERE course_id = ? ORDER BY `order` ASC');
$stmt->bind_param('i', $course_id);
$stmt->execute();
$modules_result = $stmt->get_result();
$modules = [];
while ($module = $modules_result->fetch_assoc()) {
    $lesson_stmt = $conn->prepare('SELECT id, title FROM lessons WHERE module_id = ? ORDER BY `order` ASC');
    $lesson_stmt->bind_param('i', $module['id']);
    $lesson_stmt->execute();
    $module['lessons'] = $lesson_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $lesson_stmt->close();
    $modules[] = $module;
}
$stmt->close();
$conn->close();

$pageTitle = "Gérer le contenu du cours";
include '../partials/header.php';
?>

<div class="container">
    <a href="index.php">&larr; Retour aux cours</a>
    <div class="page-header-admin">
        <h1>Contenu de "<?php echo htmlspecialchars($course['title']); ?>"</h1>
    </div>

    <!-- Bouton pour ajouter un module -->
    <div class="admin-form-container">
        <a href="../modules/create.php?course_id=<?php echo $course_id; ?>" class="btn-primary">+ Ajouter un nouveau module</a>
    </div>

    <!-- Liste des modules et leçons -->
    <div class="content-management" id="modules-list">
        <?php if (empty($modules)): ?>
            <p>Ce cours ne contient aucun module pour le moment.</p>
        <?php else: ?>
            <?php foreach ($modules as $module): ?>
                <div class="module-container" data-id="<?php echo $module['id']; ?>">
                    <div class="module-header">
                        <h4><?php echo htmlspecialchars($module['title']); ?></h4>
                        <div class="actions">
                            <a href="../modules/edit.php?id=<?php echo $module['id']; ?>" class="btn-secondary btn-sm">Modifier</a>
                            <a href="../modules/delete.php?id=<?php echo $module['id']; ?>" class="btn-danger btn-sm" onclick="return confirm('Supprimer ce module et toutes ses leçons ?');">Supprimer</a>
                        </div>
                    </div>
                    <ul class="lesson-list" data-module-id="<?php echo $module['id']; ?>">
                        <?php if (empty($module['lessons'])): ?>
                            <li class="no-lessons">Aucune leçon dans ce module.</li>
                        <?php else: ?>
                            <?php foreach ($module['lessons'] as $lesson): ?>
                                <li data-id="<?php echo $lesson['id']; ?>">
                                    <span><?php echo htmlspecialchars($lesson['title']); ?></span>
                                    <div class="actions">
                                        <a href="../lessons/edit.php?id=<?php echo $lesson['id']; ?>" class="btn-secondary btn-sm">Modifier</a>
                                        <a href="../lessons/delete.php?id=<?php echo $lesson['id']; ?>" class="btn-danger btn-sm" onclick="return confirm('Supprimer cette leçon ?');">Supprimer</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                     <a href="../lessons/create.php?module_id=<?php echo $module['id']; ?>" class="add-lesson-link">+ Ajouter une leçon</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
