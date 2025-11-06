<?php
require 'auth_check.php';
require '../config/database.php';

$pageTitle = "Tableau de bord Admin";
include 'partials/header.php';

// Logique pour récupérer des statistiques (à venir)

?>

<div class="container">
    <h1>Tableau de bord Administrateur</h1>
    <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
    
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'superadmin'): ?>
        <div class="alert alert-gold">
            <strong>👑 Super Administrateur</strong> - Vous avez tous les droits sur la plateforme
        </div>
    <?php endif; ?>

    <div class="admin-menu">
        <a href="courses/index.php" class="menu-item">📚 Gérer les cours</a>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'superadmin'): ?>
            <a href="manage_admins.php" class="menu-item menu-item-gold">👑 Gérer les Administrateurs</a>
        <?php endif; ?>
        
        <a href="#" class="menu-item disabled">👥 Gérer les utilisateurs (à venir)</a>
    </div>

</div>

<style>
.alert-gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}
.menu-item-gold {
    background: linear-gradient(135deg, #FFD700, #FFA500) !important;
    color: #000 !important;
    font-weight: bold;
    border: 2px solid #FFA500;
}
.menu-item-gold:hover {
    background: linear-gradient(135deg, #FFA500, #FFD700) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 165, 0, 0.3);
}
</style>

<?php include 'partials/footer.php'; ?>
