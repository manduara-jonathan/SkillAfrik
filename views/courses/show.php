<div class="course-detail-container">
    <div class="course-header" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo htmlspecialchars($course['image_url']); ?>');">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
    </div>

    <div class="container course-layout">
        <div class="course-main">
            <h2>À propos de ce cours</h2>
            <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

            <h2>Contenu du cours</h2>
            <div class="modules-list">
                <?php foreach ($modules as $module): ?>
                    <a href="<?php echo $is_enrolled && $module['first_lesson_id'] ? 'lesson.php?id=' . $module['first_lesson_id'] : '#'; ?>" class="module-item <?php echo !$is_enrolled ? 'disabled' : ''; ?>">
                        <?php echo htmlspecialchars($module['title']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="course-sidebar">
            <div class="enroll-box">
                <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="Image du cours">
                <?php if ($is_enrolled): ?>
                    <?php if ($is_course_completed): ?>
                    <a href="generate_certificate.php?course_id=<?php echo $course_id; ?>" class="btn-enroll">Obtenir mon certificat</a>
                <?php else: ?>
                    <a href="#" class="btn-enroll enrolled">Continuer le cours</a>
                <?php endif; ?>
                    <p>Vous êtes déjà inscrit à ce cours.</p>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <form action="course.php?id=<?php echo $course_id; ?>" method="POST">
                        <?php csrf_input(); ?>
                        <button type="submit" name="enroll" class="btn-enroll">S'inscrire au cours</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-enroll">Connectez-vous pour vous inscrire</a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>
