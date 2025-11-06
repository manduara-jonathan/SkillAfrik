<?php
session_start();
require '../config/database.php';

$pageTitle = "Parcours Culture Numérique - SkillAfrik";
include '../views/partials/header.php';

$conn = db_connect();

// Récupérer les cours de la catégorie "Culture Numérique"
$category_name = 'Culture Numérique';
$stmt = $conn->prepare('SELECT c.* FROM courses c JOIN categories cat ON c.category_id = cat.id WHERE cat.name = ? AND c.is_published = 1');
$stmt->bind_param('s', $category_name);
$stmt->execute();
$courses_result = $stmt->get_result();

?>

<div class="parcours-hero" style="background-image: url('../public/images/parcours/culture-numerique-bg.jpg');">
    <div class="container">
        <h1>Culture Numérique : Maîtrisez les Essentiels du Digital</h1>
        <p>Développez les compétences fondamentales pour naviguer, communiquer et travailler efficacement dans le monde numérique d'aujourd'hui.</p>
    </div>
</div>

<div class="container parcours-page">
    <div class="parcours-description card">
        <h2>À propos de ce parcours</h2>
        <p>Dans un monde de plus en plus connecté, la culture numérique est devenue une compétence aussi essentielle que lire ou écrire. Ce parcours est conçu pour vous donner les clés de compréhension de l'écosystème digital, que vous soyez un parfait débutant ou que vous cherchiez à solidifier vos bases.</p>
        <p>De la navigation sécurisée sur Internet à la maîtrise des outils bureautiques, en passant par la compréhension des réseaux sociaux et la protection de vos données personnelles, nous vous guiderons pas à pas pour faire de vous un citoyen numérique averti et compétent.</p>
        
        <h3>Ce que vous allez apprendre :</h3>
        <ul>
            <li><i class="fas fa-check-circle"></i> Naviguer sur Internet en toute sécurité</li>
            <li><i class="fas fa-check-circle"></i> Utiliser efficacement les outils de bureautique (traitement de texte, tableur)</li>
            <li><i class="fas fa-check-circle"></i> Communiquer et collaborer en ligne (emails, visioconférence)</li>
            <li><i class="fas fa-check-circle"></i> Comprendre le fonctionnement des réseaux sociaux</li>
            <li><i class="fas fa-check-circle"></i> Protéger votre identité et vos données personnelles</li>
        </ul>
        
        <h3>Pour qui est ce parcours ?</h3>
        <p>Ce parcours est idéal pour les étudiants, les professionnels en reconversion, les entrepreneurs, ou toute personne souhaitant acquérir une autonomie et une confiance solides dans l'utilisation des technologies numériques au quotidien.</p>
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
