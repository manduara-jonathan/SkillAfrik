# 🎓 SkillAfrik - Plateforme d'Apprentissage en Ligne

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

**SkillAfrik** est une plateforme d'apprentissage en ligne moderne conçue pour démocratiser l'accès à l'éducation numérique en Afrique. Elle permet aux utilisateurs de suivre des cours structurés avec des modules, des leçons et des quiz interactifs.

---

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#️-configuration)
- [Structure du projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [Documentation technique](#-documentation-technique)
- [Sécurité](#-sécurité)
- [Contribution](#-contribution)
- [Support](#-support)
- [Licence](#-licence)

---

## ✨ Fonctionnalités

### Pour les utilisateurs
- ✅ Inscription et connexion sécurisées
- ✅ Navigation intuitive des cours disponibles
- ✅ Suivi de progression personnalisé
- ✅ Leçons texte et vidéo (YouTube)
- ✅ Quiz interactifs avec correction automatique
- ✅ Certificats de complétion
- ✅ Tableau de bord personnalisé

### Pour les administrateurs
- ✅ Gestion complète des cours (CRUD)
- ✅ Gestion des modules et leçons
- ✅ Création et gestion de quiz
- ✅ Réorganisation par glisser-déposer
- ✅ Upload d'images pour les cours
- ✅ Statistiques et suivi des utilisateurs

### Sécurité
- ✅ Protection CSRF sur tous les formulaires
- ✅ Protection contre les injections SQL (requêtes préparées)
- ✅ Protection XSS (échappement des données)
- ✅ Hachage sécurisé des mots de passe (bcrypt)
- ✅ Validation des données côté serveur
- ✅ Gestion des sessions sécurisée

---

## 🔧 Prérequis

### Logiciels requis
- **PHP** >= 7.4
- **MySQL** >= 5.7 ou **MariaDB** >= 10.2
- **Apache** ou **Nginx** avec mod_rewrite
- **Composer** (optionnel, pour les dépendances futures)

### Extensions PHP requises
- `mysqli`
- `pdo_mysql`
- `mbstring`
- `json`
- `session`
- `gd` (pour la manipulation d'images)

---

## 📦 Installation

### 👨‍💻 Auteur

**Jonathan Manduara Tshimpaka**  
Créateur et Propriétaire de SkillAfrik

- Email: manduarajonathan.m@gmail.com
- Téléphone: +243890868095
- GitHub: [@votre-username](https://github.com/votre-username)

### 1. Cloner le projet

```bash
git clone https://github.com/votre-username/skillafrik.git
cd skillafrik
```

### 2. Configurer la base de données

#### Créer la base de données
```sql
CREATE DATABASE skillafrik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Importer le schéma
```bash
mysql -u root -p skillafrik < database/skillafrik_complete.sql
```

### 3. Configurer l'application

Copiez le fichier de configuration et modifiez les paramètres :

```bash
cp config/database.example.php config/database.php
```

Éditez `config/database.php` :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_NAME', 'skillafrik');
```

### 4. Configurer les permissions

```bash
# Linux/Mac
chmod 755 -R .
chmod 777 -R uploads/
chmod 777 -R storage/

# Windows (PowerShell en admin)
icacls uploads /grant Everyone:F /T
icacls storage /grant Everyone:F /T
```

### 5. Configurer le serveur web

#### Apache (.htaccess déjà inclus)
Assurez-vous que `mod_rewrite` est activé :
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

#### Nginx
Ajoutez cette configuration :
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Accéder à l'application

```
http://localhost/SkillAfrik
```

### 7. Connexion par défaut

**Administrateur :**
- Email : `admin@skillafrik.com`
- Mot de passe : `admin123`

**Utilisateur test :**
- Email : `user@example.com`
- Mot de passe : `password123`

⚠️ **Important** : Changez ces mots de passe en production !

---

## ⚙️ Configuration

### Variables d'environnement

Créez un fichier `.env` à la racine (optionnel) :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_HOST=localhost
DB_NAME=skillafrik
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=votre-email@gmail.com
MAIL_PASS=votre-mot-de-passe
```

### Configuration du mail

Pour activer la récupération de mot de passe par email, configurez les paramètres SMTP dans `config/mail.php`.

---

## 📁 Structure du projet

```
SkillAfrik/
│
├── 📁 admin/                    # Interface d'administration
│   ├── index.php                # Dashboard admin
│   ├── auth_check.php           # Vérification authentification
│   ├── update_order.php         # Réorganisation drag & drop
│   │
│   ├── 📁 courses/              # Gestion des cours
│   │   ├── index.php            # Liste des cours
│   │   ├── create.php           # Créer un cours
│   │   ├── edit.php             # Modifier un cours
│   │   ├── delete.php           # Supprimer un cours
│   │   └── content.php          # Gérer le contenu
│   │
│   ├── 📁 modules/              # Gestion des modules
│   │   ├── create.php           # Créer un module
│   │   ├── edit.php             # Modifier un module
│   │   └── delete.php           # Supprimer un module
│   │
│   ├── 📁 lessons/              # Gestion des leçons
│   │   ├── create.php           # Créer une leçon
│   │   ├── edit.php             # Modifier une leçon
│   │   └── delete.php           # Supprimer une leçon
│   │
│   ├── 📁 quizzes/              # Gestion des quiz
│   │   └── manage.php           # Gérer les quiz
│   │
│   ├── 📁 users/                # Gestion des utilisateurs (futur)
│   │
│   └── 📁 partials/             # Vues partielles admin
│       ├── header.php
│       └── footer.php
│
├── 📁 views/                    # Vues utilisateur
│   ├── 📁 auth/                 # Authentification
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── 📁 courses/              # Cours
│   │   ├── index.php            # Liste des cours
│   │   ├── show.php             # Détails d'un cours
│   │   └── lesson.php           # Affichage d'une leçon
│   │
│   ├── 📁 user/                 # Utilisateur
│   │   └── dashboard.php        # Tableau de bord
│   │
│   ├── 📁 errors/               # Pages d'erreur
│   │   └── 404.php
│   │
│   └── 📁 partials/             # Vues partielles
│       ├── header.php
│       └── footer.php
│
├── 📁 config/                   # Configuration
│   ├── database.php             # Configuration BDD
│   └── csrf.php                 # Protection CSRF
│
├── 📁 database/                 # Base de données
│   └── skillafrik_complete.sql  # Schéma complet
│
├── 📁 docs/                     # Documentation
│   ├── INSTALLATION.md          # Guide d'installation
│   ├── API.md                   # Documentation API (futur)
│   ├── ARCHITECTURE.md          # Architecture technique
│   └── CONTRIBUTING.md          # Guide de contribution
│
├── 📁 lib/                      # Bibliothèques tierces
│   └── fpdf/                    # Génération PDF
│
├── 📁 public/                   # Assets publics
│   ├── 📁 css/                  # Feuilles de style
│   ├── 📁 js/                   # Scripts JavaScript
│   └── 📁 images/               # Images
│
├── 📁 storage/                  # Stockage
│   ├── 📁 logs/                 # Logs applicatifs
│   ├── 📁 cache/                # Cache
│   └── 📁 sessions/             # Sessions
│
├── 📁 uploads/                  # Fichiers uploadés
│   └── courses/                 # Images des cours
│
├── .htaccess                    # Configuration Apache
├── .gitignore                   # Fichiers ignorés par Git
├── README.md                    # Ce fichier
└── index.php                    # Point d'entrée
```

---

## 🚀 Utilisation

### Pour les utilisateurs

#### 1. S'inscrire
```
http://localhost/SkillAfrik/register.php
```

#### 2. Se connecter
```
http://localhost/SkillAfrik/login.php
```

#### 3. Parcourir les cours
```
http://localhost/SkillAfrik/courses.php
```

#### 4. Suivre un cours
- Cliquez sur un cours
- Suivez les modules dans l'ordre
- Complétez les leçons
- Passez les quiz

### Pour les administrateurs

#### 1. Accéder au panel admin
```
http://localhost/SkillAfrik/admin
```

#### 2. Gérer les cours
- **Créer** : Admin → Gérer les cours → Créer un cours
- **Modifier** : Cliquez sur "Modifier" à côté d'un cours
- **Supprimer** : Cliquez sur "Supprimer" (confirmation requise)

#### 3. Gérer le contenu
- Cliquez sur "Gérer le contenu" d'un cours
- Ajoutez des modules
- Ajoutez des leçons à chaque module
- Créez des quiz pour les leçons

#### 4. Réorganiser
- Utilisez le glisser-déposer pour réorganiser les modules et leçons

---

## 📖 Documentation technique

### Architecture

SkillAfrik utilise une architecture MVC simplifiée :

- **Modèle** : Accès direct à la base de données via MySQLi
- **Vue** : Templates PHP avec séparation des vues utilisateur/admin
- **Contrôleur** : Logique dans les fichiers PHP principaux

### Base de données

#### Tables principales

**users** : Utilisateurs de la plateforme
```sql
- id, username, email, password, role, created_at
```

**courses** : Cours disponibles
```sql
- id, title, description, image_url, created_at
```

**modules** : Modules d'un cours
```sql
- id, course_id, title, order
```

**lessons** : Leçons d'un module
```sql
- id, module_id, title, content_type, content, order
```

**quizzes** : Quiz associés aux leçons
```sql
- id, lesson_id, title
```

**questions** : Questions d'un quiz
```sql
- id, quiz_id, question_text
```

**answers** : Réponses possibles
```sql
- id, question_id, answer_text, is_correct
```

**enrollments** : Inscriptions aux cours
```sql
- id, user_id, course_id, enrolled_at, progress
```

**lesson_completions** : Leçons complétées
```sql
- id, user_id, lesson_id, completed_at
```

**quiz_attempts** : Tentatives de quiz
```sql
- id, user_id, quiz_id, score, attempted_at
```

### Sécurité

#### Protection CSRF
```php
// Générer un token
generate_csrf_token();

// Afficher dans un formulaire
csrf_input();

// Valider
if (validate_csrf_token()) {
    // Traiter le formulaire
}
```

#### Requêtes préparées
```php
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
```

#### Échappement XSS
```php
echo htmlspecialchars($user_input);
```

---

## 🔒 Sécurité

### Bonnes pratiques implémentées

✅ **Authentification**
- Hachage bcrypt pour les mots de passe
- Sessions sécurisées avec régénération d'ID
- Timeout de session après inactivité

✅ **Validation**
- Validation côté serveur de toutes les entrées
- Filtrage des types de fichiers uploadés
- Limitation de la taille des uploads

✅ **Protection**
- Protection CSRF sur tous les formulaires
- Requêtes SQL préparées (prévention injection SQL)
- Échappement HTML (prévention XSS)
- Vérification des permissions (admin/user)

✅ **Configuration**
- Fichiers sensibles hors du webroot
- `.htaccess` pour bloquer l'accès aux fichiers sensibles
- Logs d'erreurs sécurisés

### Recommandations pour la production

⚠️ **Avant de déployer :**

1. Changez tous les mots de passe par défaut
2. Configurez HTTPS (certificat SSL)
3. Désactivez l'affichage des erreurs PHP
4. Activez les logs d'erreurs
5. Configurez des sauvegardes automatiques
6. Limitez les tentatives de connexion
7. Ajoutez un WAF (Web Application Firewall)
8. Configurez des headers de sécurité

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Consultez [CONTRIBUTING.md](docs/CONTRIBUTING.md) pour plus de détails.

### Comment contribuer

1. Forkez le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

---

## 📞 Support

Pour toute question ou problème :

- **Email** : manduarajonathan.m@gmail.com
- **Téléphone** : +243890868095
- **GitHub Issues** : [Créer un ticket](https://github.com/votre-username/skillafrik/issues)
- **Documentation** : Consultez le dossier `docs/`

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🙏 Remerciements

- Tous les contributeurs qui ont participé au projet
- La communauté open-source pour les outils et bibliothèques
- Les utilisateurs pour leurs retours et suggestions

---

## 🗺️ Roadmap

### Version 1.1 (À venir)
- [ ] Système de notifications en temps réel
- [ ] Chat entre utilisateurs
- [ ] Forum de discussion par cours
- [ ] Badges et gamification

### Version 1.2
- [ ] Application mobile (React Native)
- [ ] API REST complète
- [ ] Intégration paiement (Stripe, PayPal)
- [ ] Cours payants et abonnements

### Version 2.0
- [ ] Live streaming de cours
- [ ] Visioconférence intégrée
- [ ] Marketplace de cours
- [ ] Système d'affiliation

---

**Développé avec ❤️ pour l'éducation numérique en Afrique**

**Créateur & Propriétaire** : Jonathan Manduara Tshimpaka  
**Contact** : manduarajonathan.m@gmail.com | +243890868095

**© 2025 SkillAfrik. Tous droits réservés.**
