<div class="dashboard-container">
    <aside class="dashboard-sidebar">
        <!-- Le contenu de la sidebar pourrait être ajouté ici si nécessaire -->
    </aside>
    <div class="dashboard-main-content">
        <nav class="dashboard-nav">
            <a href="#" class="nav-item active"><i class="icon-home"></i> Accueil du site</a>
            <a href="#" class="nav-item"><i class="icon-badge"></i> Badges</a>
            <a href="#" class="nav-item"><i class="icon-courses"></i> Mes cours</a>
            <a href="courses.php" class="nav-item"><i class="icon-all-courses"></i> Tous les cours</a>
        </nav>

        <div class="dashboard-content-area">
            <div class="courses-overview">
                <h2>Vue d'ensemble des cours</h2>
                <div class="filters">
                    <select>
                        <option>Tout</option>
                    </select>
                    <input type="text" placeholder="Rechercher">
                    <select>
                        <option>Trier par nom de cours</option>
                    </select>
                    <select>
                        <option>Carte</option>
                    </select>
                </div>
                <div class="courses-grid">
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <a href="course.php?id=<?php echo $course['id']; ?>" class="course-card-link">
                                <div class="course-card">
                                    <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="">
                                    <div class="course-card-content">
                                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: <?php echo $course['progress']; ?>%;"></div>
                                        </div>
                                        <p><?php echo $course['progress']; ?>% terminé</p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Vous n'êtes inscrit à aucun cours pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="recent-items">
                <h2>Éléments consultés récemment</h2>
                <ul>
                    <?php foreach ($recent_items as $item): ?>
                        <li>
                            <div class="item-icon"></div>
                            <div class="item-details">
                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                <span><?php echo htmlspecialchars($item['course']); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button class="btn-more">Montrer plus d'éléments</button>
            </div>
        </div>
    </div>
</div>
