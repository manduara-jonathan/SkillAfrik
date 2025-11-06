<div class="container">
    <div class="page-header">
        <h1>Tous les cours</h1>
        <p>Parcourez notre catalogue de formations pour trouver celle qui vous convient.</p>
    </div>

    <div class="all-courses-grid">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <div class="course-item-card">
                    <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Image du cours">
                    <div class="course-item-content">
                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                        <a href="course.php?id=<?php echo $course['id']; ?>" class="btn-view-course">Voir le cours</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun cours n'est disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
