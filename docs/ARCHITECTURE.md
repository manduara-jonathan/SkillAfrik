# 🏗️ Architecture Technique - SkillAfrik

Ce document décrit l'architecture technique de la plateforme SkillAfrik.

---

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Architecture logicielle](#architecture-logicielle)
- [Base de données](#base-de-données)
- [Sécurité](#sécurité)
- [Performance](#performance)
- [API Future](#api-future)

---

## 🎯 Vue d'ensemble

### Stack technique

| Couche | Technologie | Version |
|--------|-------------|---------|
| **Frontend** | HTML5, CSS3, JavaScript | - |
| **Backend** | PHP | 7.4+ |
| **Base de données** | MySQL | 5.7+ |
| **Serveur web** | Apache | 2.4+ |
| **Bibliothèques** | FPDF | 1.8+ |

### Architecture globale

```
┌─────────────────────────────────────────────────────────────┐
│                         CLIENT                               │
│                    (Navigateur Web)                          │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP/HTTPS
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVEUR WEB                               │
│                   (Apache/Nginx)                             │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                  APPLICATION PHP                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Views      │  │  Controllers │  │   Config     │      │
│  │  (Templates) │  │   (Logic)    │  │  (Database)  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└────────────────────────┬────────────────────────────────────┘
                         │ MySQLi
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   BASE DE DONNÉES                            │
│                      (MySQL)                                 │
└─────────────────────────────────────────────────────────────┘
```

---

## 🏛️ Architecture logicielle

### Pattern MVC simplifié

SkillAfrik utilise une architecture MVC (Model-View-Controller) simplifiée :

```
SkillAfrik/
│
├── Views (Vues)
│   ├── views/          # Vues utilisateur
│   └── admin/          # Vues admin
│
├── Controllers (Contrôleurs)
│   ├── *.php           # Fichiers PHP à la racine
│   └── admin/*.php     # Contrôleurs admin
│
└── Models (Modèles)
    └── config/database.php  # Accès BDD
```

### Flux de requête

```
1. Client envoie requête
   ↓
2. Apache/Nginx reçoit
   ↓
3. .htaccess redirige vers le bon fichier PHP
   ↓
4. PHP charge la configuration (database.php, csrf.php)
   ↓
5. Vérification authentification (si nécessaire)
   ↓
6. Traitement de la logique métier
   ↓
7. Requêtes à la base de données (MySQLi)
   ↓
8. Chargement de la vue (include header/footer)
   ↓
9. Rendu HTML envoyé au client
```

### Structure des fichiers

#### Fichiers utilisateur (racine)

```php
// index.php - Page d'accueil
<?php
session_start();
$pageTitle = "Accueil";
include 'views/partials/header.php';
// Contenu
include 'views/partials/footer.php';
?>
```

#### Fichiers admin

```php
// admin/courses/index.php - Liste des cours
<?php
require '../auth_check.php';  // Vérification admin
require '../../config/database.php';

$conn = db_connect();
// Logique métier
$conn->close();

include '../partials/header.php';
// Affichage
include '../partials/footer.php';
?>
```

---

## 🗄️ Base de données

### Schéma relationnel

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │
       │ 1:N
       ▼
┌─────────────────┐      ┌──────────────┐
│  enrollments    │──N:1─│   courses    │
└─────────────────┘      └──────┬───────┘
                                │
                                │ 1:N
                                ▼
                         ┌──────────────┐
                         │   modules    │
                         └──────┬───────┘
                                │
                                │ 1:N
                                ▼
                         ┌──────────────┐
                         │   lessons    │
                         └──────┬───────┘
                                │
                    ┌───────────┴───────────┐
                    │ 1:N                   │ 1:1
                    ▼                       ▼
            ┌──────────────┐        ┌──────────────┐
            │lesson_compl. │        │   quizzes    │
            └──────────────┘        └──────┬───────┘
                                           │
                                           │ 1:N
                                           ▼
                                    ┌──────────────┐
                                    │  questions   │
                                    └──────┬───────┘
                                           │
                                ┌──────────┴──────────┐
                                │ 1:N                 │ 1:N
                                ▼                     ▼
                         ┌──────────────┐    ┌──────────────┐
                         │   answers    │    │quiz_attempts │
                         └──────────────┘    └──────────────┘
```

### Tables détaillées

#### users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### courses
```sql
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### modules
```sql
CREATE TABLE modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    `order` INT DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_order (course_id, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### lessons
```sql
CREATE TABLE lessons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content_type ENUM('text', 'video') NOT NULL,
    content TEXT,
    `order` INT DEFAULT 0,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    INDEX idx_module_order (module_id, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### quizzes
```sql
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lesson_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    INDEX idx_lesson (lesson_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### questions
```sql
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz (quiz_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### answers
```sql
CREATE TABLE answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### enrollments
```sql
CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    progress INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    INDEX idx_user (user_id),
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### lesson_completions
```sql
CREATE TABLE lesson_completions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_completion (user_id, lesson_id),
    INDEX idx_user (user_id),
    INDEX idx_lesson (lesson_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### quiz_attempts
```sql
CREATE TABLE quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_quiz (quiz_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Optimisations

#### Index
- Index sur les clés étrangères pour les jointures
- Index sur les champs de recherche (email, role)
- Index composites pour les tris (course_id, order)

#### Contraintes
- `ON DELETE CASCADE` pour la suppression en cascade
- `UNIQUE` pour éviter les doublons
- `NOT NULL` pour les champs obligatoires

---

## 🔒 Sécurité

### Couches de sécurité

```
┌─────────────────────────────────────────────────────────┐
│  1. AUTHENTIFICATION                                     │
│     - Sessions PHP sécurisées                           │
│     - Hachage bcrypt des mots de passe                  │
│     - Vérification des rôles (admin/user)               │
└─────────────────────────────────────────────────────────┘
                         ▼
┌─────────────────────────────────────────────────────────┐
│  2. VALIDATION                                           │
│     - Validation côté serveur                           │
│     - Filtrage des entrées (filter_var)                 │
│     - Vérification des types de fichiers                │
└─────────────────────────────────────────────────────────┘
                         ▼
┌─────────────────────────────────────────────────────────┐
│  3. PROTECTION                                           │
│     - CSRF tokens sur tous les formulaires              │
│     - Requêtes préparées (SQL Injection)                │
│     - Échappement HTML (XSS)                            │
└─────────────────────────────────────────────────────────┘
                         ▼
┌─────────────────────────────────────────────────────────┐
│  4. CONFIGURATION                                        │
│     - .htaccess pour bloquer fichiers sensibles         │
│     - Headers de sécurité                               │
│     - Logs d'erreurs sécurisés                          │
└─────────────────────────────────────────────────────────┘
```

### Implémentation

#### Protection CSRF

```php
// config/csrf.php
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function csrf_input() {
    echo '<input type="hidden" name="csrf_token" value="' . 
         htmlspecialchars($_SESSION['csrf_token']) . '">';
}

function validate_csrf_token() {
    return isset($_POST['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
```

#### Requêtes préparées

```php
// Mauvais (vulnérable à SQL Injection)
$query = "SELECT * FROM users WHERE email = '$email'";

// Bon (sécurisé)
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
```

#### Échappement XSS

```php
// Toujours échapper les données utilisateur
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
```

#### Authentification

```php
// admin/auth_check.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
```

---

## ⚡ Performance

### Optimisations implémentées

#### 1. Base de données
- Index sur les colonnes fréquemment recherchées
- Requêtes optimisées avec LIMIT
- Connexion persistante MySQLi

#### 2. PHP
- Sessions optimisées
- Fermeture des connexions après usage
- Pas de requêtes dans les boucles

#### 3. Frontend
- CSS minifié en production
- JavaScript différé
- Images optimisées

### Recommandations futures

#### Cache
```php
// Implémenter un système de cache
- Memcached pour les sessions
- Redis pour les données fréquentes
- Cache de requêtes MySQL
```

#### CDN
```
- Héberger les assets statiques sur un CDN
- Utiliser un CDN pour les bibliothèques JS
```

#### Compression
```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript
</IfModule>
```

---

## 🔮 API Future

### Architecture REST API (Planifiée v2.0)

```
┌─────────────────────────────────────────────────────────┐
│                    CLIENT                                │
│              (Web, Mobile, Desktop)                      │
└────────────────────────┬────────────────────────────────┘
                         │ JSON/REST
                         ▼
┌─────────────────────────────────────────────────────────┐
│                   API GATEWAY                            │
│              (Authentication, Rate Limiting)             │
└────────────────────────┬────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┐
        ▼                ▼                ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   Courses    │  │    Users     │  │    Quiz      │
│   Service    │  │   Service    │  │   Service    │
└──────────────┘  └──────────────┘  └──────────────┘
        │                │                │
        └────────────────┼────────────────┘
                         ▼
                  ┌──────────────┐
                  │   Database   │
                  └──────────────┘
```

### Endpoints prévus

```
GET    /api/v1/courses              # Liste des cours
GET    /api/v1/courses/{id}         # Détails d'un cours
POST   /api/v1/courses              # Créer un cours (admin)
PUT    /api/v1/courses/{id}         # Modifier un cours (admin)
DELETE /api/v1/courses/{id}         # Supprimer un cours (admin)

GET    /api/v1/users/me             # Profil utilisateur
PUT    /api/v1/users/me             # Modifier profil
GET    /api/v1/users/me/courses     # Mes cours

POST   /api/v1/auth/login           # Connexion
POST   /api/v1/auth/register        # Inscription
POST   /api/v1/auth/logout          # Déconnexion
POST   /api/v1/auth/refresh         # Rafraîchir token
```

---

## 📊 Diagrammes

### Diagramme de séquence - Inscription à un cours

```
User          Frontend        Backend         Database
 │                │              │               │
 │   Clic "S'inscrire"          │               │
 │───────────────>│              │               │
 │                │  POST /enroll.php           │
 │                │─────────────>│               │
 │                │              │ Vérif session │
 │                │              │───────────────>
 │                │              │               │
 │                │              │ INSERT enrollment
 │                │              │───────────────>
 │                │              │               │
 │                │              │<──────────────│
 │                │  Redirect    │               │
 │                │<─────────────│               │
 │  Affichage cours              │               │
 │<───────────────│              │               │
```

### Diagramme de classes (simplifié)

```
┌─────────────────┐
│      User       │
├─────────────────┤
│ - id            │
│ - username      │
│ - email         │
│ - password      │
│ - role          │
├─────────────────┤
│ + login()       │
│ + register()    │
│ + enroll()      │
└────────┬────────┘
         │
         │ enrolls in
         ▼
┌─────────────────┐
│     Course      │
├─────────────────┤
│ - id            │
│ - title         │
│ - description   │
├─────────────────┤
│ + getModules()  │
└────────┬────────┘
         │
         │ has many
         ▼
┌─────────────────┐
│     Module      │
├─────────────────┤
│ - id            │
│ - title         │
│ - order         │
├─────────────────┤
│ + getLessons()  │
└────────┬────────┘
         │
         │ has many
         ▼
┌─────────────────┐
│     Lesson      │
├─────────────────┤
│ - id            │
│ - title         │
│ - content       │
│ - type          │
├─────────────────┤
│ + complete()    │
│ + getQuiz()     │
└─────────────────┘
```

---

## 📞 Support technique

Pour toute question sur l'architecture :
- **Email** : manduarajonathan.m@gmail.com
- **Téléphone** : +243890868095
- **Documentation** : [docs/](.)
- **GitHub** : [Issues](https://github.com/votre-username/skillafrik/issues)

---

**Créateur & Propriétaire** : Jonathan Manduara Tshimpaka  
**Contact** : manduarajonathan.m@gmail.com | +243890868095

**Dernière mise à jour** : 6 Novembre 2025
