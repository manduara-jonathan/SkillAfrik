<?php
require '../auth_check.php';
require '../../config/database.php';

$conn = db_connect();
$result = $conn->query('SELECT c.id, c.title, u.username as author, c.created_at FROM courses c JOIN users u ON c.created_by = u.id ORDER BY c.created_at DESC');
$courses = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

$pageTitle = "Gérer les cours";
include '../partials/header.php';
?>

<div class="container">
    <div class="page-header-admin">
        <h1>Gérer les cours</h1>
        <a href="create.php" class="btn-primary">Ajouter un cours</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['title']); ?></td>
                        <td><?php echo htmlspecialchars($course['author']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($course['created_at'])); ?></td>
                        <td class="actions">
                            <a href="content.php?course_id=<?php echo $course['id']; ?>" class="btn-primary">Contenu</a>
                            <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn-secondary">Modifier</a>
                            <a href="delete.php?id=<?php echo $course['id']; ?>" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Aucun cours trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../partials/footer.php'; ?>
