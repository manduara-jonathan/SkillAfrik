<?php
http_response_code(404);
session_start();
include 'views/partials/header.php';
?>

<div class="error-page">
    <div class="error-container">
        <div class="error-code">404</div>
        <h1>Page non trouvée</h1>
        <p>Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>
        
        <div class="error-actions">
            <a href="index.php" class="btn-primary">Retour à l'accueil</a>
            <a href="courses.php" class="btn-secondary">Voir les cours</a>
        </div>

        <div class="error-suggestions">
            <h3>Que faire maintenant ?</h3>
            <ul>
                <li><a href="index.php">Retourner à la page d'accueil</a></li>
                <li><a href="courses.php">Parcourir nos cours</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php">Accéder à votre tableau de bord</a></li>
                <?php else: ?>
                    <li><a href="register.php">Créer un compte</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<style>
.error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.error-container {
    text-align: center;
    max-width: 600px;
}

.error-code {
    font-size: 8rem;
    font-weight: bold;
    color: #e74c3c;
    line-height: 1;
    margin-bottom: 1rem;
}

.error-page h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.error-page p {
    font-size: 1.1rem;
    color: #7f8c8d;
    margin-bottom: 2rem;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 3rem;
}

.error-suggestions {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    text-align: left;
}

.error-suggestions h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.error-suggestions ul {
    list-style: none;
    padding: 0;
}

.error-suggestions li {
    margin-bottom: 0.5rem;
}

.error-suggestions a {
    color: #3498db;
    text-decoration: none;
}

.error-suggestions a:hover {
    text-decoration: underline;
}
</style>

<?php include 'views/partials/footer.php'; ?>
