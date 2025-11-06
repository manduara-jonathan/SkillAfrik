# 🤝 Guide de Contribution - SkillAfrik

Merci de votre intérêt pour contribuer à SkillAfrik ! Ce guide vous aidera à démarrer.

---

## 📋 Table des matières

- [Code de conduite](#code-de-conduite)
- [Comment contribuer](#comment-contribuer)
- [Standards de code](#standards-de-code)
- [Processus de Pull Request](#processus-de-pull-request)
- [Signaler un bug](#signaler-un-bug)
- [Proposer une fonctionnalité](#proposer-une-fonctionnalité)

---

## 📜 Code de conduite

### Notre engagement

Nous nous engageons à faire de la participation à ce projet une expérience exempte de harcèlement pour tous, indépendamment de l'âge, de la taille corporelle, du handicap, de l'ethnicité, de l'identité et de l'expression de genre, du niveau d'expérience, de la nationalité, de l'apparence personnelle, de la race, de la religion ou de l'identité et de l'orientation sexuelles.

### Nos standards

**Comportements encouragés :**
- ✅ Utiliser un langage accueillant et inclusif
- ✅ Respecter les points de vue et expériences différents
- ✅ Accepter gracieusement les critiques constructives
- ✅ Se concentrer sur ce qui est le mieux pour la communauté
- ✅ Faire preuve d'empathie envers les autres membres

**Comportements inacceptables :**
- ❌ Langage ou imagerie sexualisés
- ❌ Commentaires insultants ou désobligeants
- ❌ Harcèlement public ou privé
- ❌ Publication d'informations privées sans permission
- ❌ Toute autre conduite inappropriée dans un cadre professionnel

---

## 🚀 Comment contribuer

### Types de contributions

Nous acceptons plusieurs types de contributions :

1. **Code**
   - Nouvelles fonctionnalités
   - Corrections de bugs
   - Améliorations de performance
   - Refactoring

2. **Documentation**
   - Amélioration de la documentation
   - Traductions
   - Tutoriels et guides
   - Corrections de fautes

3. **Design**
   - Améliorations UI/UX
   - Création d'assets
   - Prototypes

4. **Tests**
   - Tests unitaires
   - Tests d'intégration
   - Tests de sécurité

5. **Communauté**
   - Répondre aux questions
   - Aider les nouveaux contributeurs
   - Organiser des événements

### Premiers pas

#### 1. Fork le projet

```bash
# Cliquez sur "Fork" sur GitHub
# Puis clonez votre fork
git clone https://github.com/votre-username/skillafrik.git
cd skillafrik
```

#### 2. Créer une branche

```bash
# Créez une branche pour votre contribution
git checkout -b feature/ma-nouvelle-fonctionnalite

# Ou pour un bug
git checkout -b fix/correction-bug-xyz
```

#### 3. Configurer l'environnement

```bash
# Installez les dépendances
composer install  # Si applicable

# Configurez la base de données
mysql -u root -p skillafrik < database/skillafrik_complete.sql

# Copiez la configuration
cp config/database.example.php config/database.php
```

#### 4. Faites vos modifications

```bash
# Éditez les fichiers nécessaires
# Testez vos modifications localement
```

#### 5. Committez vos changements

```bash
# Ajoutez vos fichiers
git add .

# Committez avec un message clair
git commit -m "feat: ajout de la fonctionnalité X"
```

#### 6. Poussez vers GitHub

```bash
git push origin feature/ma-nouvelle-fonctionnalite
```

#### 7. Créez une Pull Request

- Allez sur GitHub
- Cliquez sur "New Pull Request"
- Remplissez le template de PR
- Attendez la review

---

## 📝 Standards de code

### Conventions de nommage

#### PHP

```php
// Classes : PascalCase
class UserController {}

// Fonctions : snake_case
function get_user_by_id($id) {}

// Variables : snake_case
$user_name = "John";

// Constantes : UPPER_SNAKE_CASE
define('MAX_LOGIN_ATTEMPTS', 3);
```

#### Base de données

```sql
-- Tables : snake_case, pluriel
CREATE TABLE users (...);
CREATE TABLE course_enrollments (...);

-- Colonnes : snake_case
user_id, created_at, is_active
```

#### Fichiers

```
- PHP : snake_case.php (user_controller.php)
- CSS : kebab-case.css (main-style.css)
- JS : kebab-case.js (form-validation.js)
```

### Style de code PHP

#### Indentation et espacement

```php
<?php
// 4 espaces pour l'indentation (pas de tabs)
function example_function($param1, $param2) {
    if ($param1 > 0) {
        return $param2;
    }
    
    return null;
}

// Espace après les mots-clés
if (...) {}
for (...) {}
while (...) {}

// Pas d'espace avant les parenthèses de fonction
function test() {}
$result = calculate();
```

#### Structure des fichiers

```php
<?php
// 1. Déclaration stricte des types (si PHP 7+)
declare(strict_types=1);

// 2. Commentaire de description du fichier
/**
 * Gestion des utilisateurs
 * 
 * @author Votre Nom
 * @version 1.0
 */

// 3. Requires/Includes
require_once 'config/database.php';
require_once 'config/csrf.php';

// 4. Vérifications de sécurité
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 5. Logique métier
$conn = db_connect();
// ...
$conn->close();

// 6. Affichage
include 'views/partials/header.php';
?>

<!-- HTML -->

<?php include 'views/partials/footer.php'; ?>
```

#### Sécurité

```php
// ✅ BON : Toujours utiliser des requêtes préparées
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();

// ❌ MAUVAIS : Concaténation directe
$query = "SELECT * FROM users WHERE email = '$email'";

// ✅ BON : Échapper les sorties
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ❌ MAUVAIS : Affichage direct
echo $user_input;

// ✅ BON : Valider les entrées
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if ($email === false) {
    die('Email invalide');
}

// ❌ MAUVAIS : Utilisation directe
$email = $_POST['email'];
```

#### Documentation

```php
/**
 * Récupère un utilisateur par son ID
 *
 * @param int $user_id L'ID de l'utilisateur
 * @return array|null Les données de l'utilisateur ou null
 * @throws Exception Si l'ID est invalide
 */
function get_user_by_id($user_id) {
    if (!is_int($user_id) || $user_id <= 0) {
        throw new Exception('ID utilisateur invalide');
    }
    
    // Logique...
    return $user_data;
}
```

### Style HTML/CSS

#### HTML

```html
<!-- Indentation : 2 espaces -->
<div class="container">
  <h1>Titre</h1>
  <p>Paragraphe</p>
</div>

<!-- Attributs : kebab-case -->
<div class="user-profile" data-user-id="123">

<!-- Toujours fermer les balises -->
<img src="image.jpg" alt="Description" />
<input type="text" />
```

#### CSS

```css
/* Sélecteurs : kebab-case */
.user-profile {
  display: flex;
  flex-direction: column;
}

/* Propriétés : ordre alphabétique (recommandé) */
.button {
  background-color: #007bff;
  border: none;
  color: white;
  padding: 10px 20px;
}

/* Commentaires clairs */
/* === HEADER === */
.header {
  /* ... */
}
```

### Style JavaScript

```javascript
// Variables : camelCase
const userName = 'John';
let userAge = 25;

// Fonctions : camelCase
function getUserById(id) {
  return users.find(user => user.id === id);
}

// Constantes : UPPER_SNAKE_CASE
const MAX_ATTEMPTS = 3;

// Classes : PascalCase
class UserManager {
  constructor() {
    // ...
  }
}

// Utiliser const par défaut, let si nécessaire
const items = [];
let count = 0;

// Éviter var
// ❌ var x = 10;
// ✅ const x = 10;
```

---

## 🔄 Processus de Pull Request

### Template de PR

Lorsque vous créez une PR, utilisez ce template :

```markdown
## Description
Brève description de vos changements.

## Type de changement
- [ ] Bug fix (correction de bug)
- [ ] New feature (nouvelle fonctionnalité)
- [ ] Breaking change (changement cassant la compatibilité)
- [ ] Documentation update (mise à jour de la documentation)

## Comment tester
Étapes pour tester vos changements :
1. Aller sur...
2. Cliquer sur...
3. Vérifier que...

## Checklist
- [ ] Mon code suit les standards du projet
- [ ] J'ai testé mes modifications
- [ ] J'ai mis à jour la documentation
- [ ] J'ai ajouté des tests (si applicable)
- [ ] Tous les tests passent
- [ ] Mon code ne génère pas de warnings

## Screenshots (si applicable)
Ajoutez des captures d'écran si pertinent.

## Notes additionnelles
Toute information supplémentaire utile.
```

### Processus de review

1. **Soumission** : Vous créez la PR
2. **Review automatique** : Les tests automatiques s'exécutent
3. **Review manuelle** : Un mainteneur examine votre code
4. **Modifications** : Vous apportez les changements demandés
5. **Approbation** : La PR est approuvée
6. **Merge** : Votre code est fusionné !

### Critères d'acceptation

Votre PR sera acceptée si :

- ✅ Le code suit les standards du projet
- ✅ Tous les tests passent
- ✅ La documentation est à jour
- ✅ Pas de conflits avec la branche principale
- ✅ Les changements sont pertinents et utiles
- ✅ Le code est sécurisé

---

## 🐛 Signaler un bug

### Avant de signaler

1. **Vérifiez** que le bug n'a pas déjà été signalé
2. **Testez** sur la dernière version
3. **Reproduisez** le bug de manière fiable

### Template de bug report

```markdown
## Description du bug
Description claire et concise du bug.

## Étapes pour reproduire
1. Aller sur '...'
2. Cliquer sur '...'
3. Faire défiler jusqu'à '...'
4. Voir l'erreur

## Comportement attendu
Ce qui devrait se passer.

## Comportement actuel
Ce qui se passe réellement.

## Screenshots
Si applicable, ajoutez des captures d'écran.

## Environnement
- OS: [ex: Windows 10]
- Navigateur: [ex: Chrome 96]
- Version PHP: [ex: 7.4]
- Version MySQL: [ex: 5.7]

## Logs d'erreur
```
Collez les logs d'erreur ici
```

## Informations additionnelles
Tout autre contexte utile.
```

---

## 💡 Proposer une fonctionnalité

### Template de feature request

```markdown
## Problème à résoudre
Décrivez le problème que cette fonctionnalité résoudrait.

## Solution proposée
Décrivez la solution que vous aimeriez voir.

## Alternatives considérées
Décrivez les alternatives que vous avez envisagées.

## Bénéfices
- Bénéfice 1
- Bénéfice 2

## Complexité estimée
- [ ] Facile (< 1 jour)
- [ ] Moyenne (1-3 jours)
- [ ] Difficile (> 3 jours)

## Informations additionnelles
Tout autre contexte, screenshots, mockups, etc.
```

---

## 🎯 Priorités du projet

### Priorité HAUTE
- Sécurité
- Corrections de bugs critiques
- Performance

### Priorité MOYENNE
- Nouvelles fonctionnalités
- Améliorations UX
- Documentation

### Priorité BASSE
- Refactoring
- Optimisations mineures
- Améliorations cosmétiques

---

## 📞 Communication

### Canaux

- **GitHub Issues** : Pour les bugs et features
- **GitHub Discussions** : Pour les questions générales
- **Email** : manduarajonathan.m@gmail.com
- **Téléphone** : +243890868095
- **Discord** : [Lien vers le serveur](#)

### Temps de réponse

- Issues : 24-48h
- Pull Requests : 48-72h
- Emails : 3-5 jours

---

## 🏆 Reconnaissance

Tous les contributeurs seront :

- ✅ Listés dans le fichier CONTRIBUTORS.md
- ✅ Mentionnés dans les release notes
- ✅ Remerciés publiquement sur nos réseaux sociaux

### Top contributeurs

Les contributeurs les plus actifs recevront :

- 🥇 Badge "Core Contributor"
- 🎁 Goodies SkillAfrik
- 📜 Certificat de contribution

---

## 📚 Ressources

### Documentation
- [README.md](../README.md)
- [INSTALLATION.md](INSTALLATION.md)
- [ARCHITECTURE.md](ARCHITECTURE.md)

### Tutoriels
- [Créer sa première fonctionnalité](#)
- [Corriger un bug](#)
- [Écrire des tests](#)

### Outils recommandés
- **IDE** : VS Code, PHPStorm
- **Extensions** : PHP Intelephense, ESLint
- **Outils** : Git, Composer, MySQL Workbench

---

## ❓ Questions fréquentes

### Comment puis-je commencer à contribuer ?

Consultez les issues avec le label `good first issue` sur GitHub.

### Dois-je signer un CLA ?

Non, nous n'exigeons pas de CLA (Contributor License Agreement).

### Puis-je contribuer sans coder ?

Oui ! Documentation, design, traductions, tests, support communautaire sont tous précieux.

### Combien de temps pour qu'une PR soit reviewée ?

Généralement 48-72h. Les PRs simples sont souvent reviewées plus rapidement.

### Ma PR a été refusée, que faire ?

Lisez les commentaires, apportez les modifications demandées, ou discutez-en avec les mainteneurs.

---

**Merci de contribuer à SkillAfrik ! 🎓**

**Ensemble, rendons l'éducation accessible à tous en Afrique ! 🌍**

---

**Créateur & Propriétaire** : Jonathan Manduara Tshimpaka  
**Contact** : manduarajonathan.m@gmail.com | +243890868095
