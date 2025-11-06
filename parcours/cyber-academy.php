<?php
session_start();
require '../config/database.php';

$pageTitle = "Parcours Cyber Academy - SkillAfrik";
include '../views/partials/header.php';

$conn = db_connect();

// Récupérer les cours de la catégorie "Cybersécurité"
$category_name = 'Cybersécurité';
$stmt = $conn->prepare('SELECT c.* FROM courses c JOIN categories cat ON c.category_id = cat.id WHERE cat.name = ? AND c.is_published = 1');
$stmt->bind_param('s', $category_name);
$stmt->execute();
$courses_result = $stmt->get_result();

?>

<div class="parcours-hero" style="background-image: url('../public/images/parcours/cyber-academy-bg.jpg');">
    <div class="container">
        <h1>Cyber Academy : Devenez un Expert en Cybersécurité</h1>
        <p>Apprenez à protéger les systèmes, les réseaux et les données contre les cyberattaques avec nos formations de pointe.</p>
    </div>
</div>

<div class="container parcours-page">
    <div class="parcours-description card">
        <h2>À propos de ce parcours</h2>
        <p>La cybersécurité est l'un des domaines les plus critiques et les plus en demande de l'industrie technologique. La Cyber Academy de SkillAfrik vous offre un parcours complet pour devenir un professionnel de la sécurité informatique, capable de défendre les infrastructures numériques contre les menaces en constante évolution.</p>
        <p>Ce parcours vous plonge au cœur de la cyberguerre moderne, en vous enseignant les techniques des attaquants (hacking éthique) pour mieux comprendre comment construire des défenses robustes. Vous apprendrez à identifier les vulnérabilités, à mettre en place des politiques de sécurité, à répondre aux incidents et à garantir la conformité réglementaire.</p>
        
        <h3>Ce que vous allez apprendre :</h3>
        <ul>
            <li><i class="fas fa-check-circle"></i> Les fondamentaux de la sécurité des réseaux et des systèmes</li>
            <li><i class="fas fa-check-circle"></i> Techniques de hacking éthique et de tests d'intrusion</li>
            <li><i class="fas fa-check-circle"></i> Analyse de malwares et réponse aux incidents</li>
            <li><i class="fas fa-check-circle"></i> Cryptographie et sécurisation des données</li>
            <li><i class="fas fa-check-circle"></i> Gestion des risques et conformité (RGPD, ISO 27001)</li>
        </ul>
        
        <h3>Pour qui est ce parcours ?</h3>
        <p>Ce parcours s'adresse aux développeurs, administrateurs système, et à toute personne passionnée par la technologie souhaitant se spécialiser dans un domaine d'avenir. Aucune connaissance préalable en sécurité n'est requise, mais une bonne compréhension des systèmes informatiques est un plus.</p>
    </div>

    <div class="parcours-courses">
        <h2>Cours disponibles dans ce parcours</h2>
        <div class="courses-grid">
            <?php if ($courses_result->num_rows > 0): ?>
                <?php while($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-card">
                        <a href="../course.php?id=<?php echo $course['id']; ?>">
                            <img src="../<?php echo htmlspecialchars($course['image_url']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                            <div class="course-card-content">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p><?php echo substr(htmlspecialchars($course['description']), 0, 100); ?>...</p>
                                <span class="btn-view-course">Voir le cours</span>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun cours disponible dans ce parcours pour le moment. Revenez bientôt !</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.parcours-hero {
    background-size: cover;
    background-position: center;
    color: white;
    padding: 80px 0;
    text-align: center;
    position: relative;
}
.parcours-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
}
.parcours-hero .container {
    position: relative;
    z-index: 2;
}
.parcours-hero h1 { font-size: 3em; }
.parcours-page { padding-top: 40px; padding-bottom: 40px; }
.parcours-description, .parcours-courses { margin-bottom: 40px; }
.parcours-description h3 { margin-top: 20px; }
.parcours-description ul { list-style: none; padding-left: 0; }
.parcours-description ul li { margin-bottom: 10px; display: flex; align-items: center; }
.parcours-description ul i { color: #28a745; margin-right: 10px; }
.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.course-card { background: #fff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s; }
.course-card:hover { transform: translateY(-5px); }
.course-card a { text-decoration: none; color: inherit; }
.course-card img { width: 100%; height: 180px; object-fit: cover; }
.course-card-content { padding: 20px; }
.course-card-content h3 { margin-top: 0; }
.btn-view-course { display: inline-block; margin-top: 15px; background: #007bff; color: white; padding: 10px 15px; border-radius: 5px; }
</style>

<?php
$stmt->close();
$conn->close();
include '../views/partials/footer.php';
?>
