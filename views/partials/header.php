<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'SkillAfrik'; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">SkillAfrik</div>
        <nav class="main-nav">
            <a href="index.php">Accueil</a>
            <a href="courses.php">Tous les cours</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Tableau de bord</a>
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a>
                <a href="register.php" class="button-signup">S'inscrire</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
