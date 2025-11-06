<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin - SkillAfrik'; ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">SkillAfrik [Admin]</div>
        <nav class="main-nav">
            <a href="index.php">Tableau de bord</a>
            <a href="manage_courses.php">Cours</a>
            <a href="../index.php" target="_blank">Voir le site</a>
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>
    <main>
