# 📦 Guide d'Installation - SkillAfrik

Ce guide vous accompagne pas à pas dans l'installation de SkillAfrik sur votre environnement de développement ou de production.

---

## 📋 Table des matières

- [Prérequis](#prérequis)
- [Installation locale](#installation-locale)
- [Installation sur serveur](#installation-sur-serveur)
- [Configuration](#configuration)
- [Vérification](#vérification)
- [Dépannage](#dépannage)

---

## 🔧 Prérequis

### Logiciels requis

| Logiciel | Version minimale | Recommandé |
|----------|------------------|------------|
| PHP | 7.4 | 8.0+ |
| MySQL | 5.7 | 8.0+ |
| Apache | 2.4 | 2.4+ |
| Composer | 2.0 | Dernière |

### Extensions PHP requises

```bash
# Vérifier les extensions installées
php -m

# Extensions nécessaires :
- mysqli
- pdo_mysql
- mbstring
- json
- session
- gd
- curl
- openssl
```

### Installer les extensions manquantes

**Ubuntu/Debian :**
```bash
sudo apt-get update
sudo apt-get install php-mysqli php-mbstring php-gd php-curl
```

**Windows (XAMPP/WAMP) :**
- Ouvrir `php.ini`
- Décommenter les lignes `extension=mysqli`, `extension=gd`, etc.
- Redémarrer Apache

---

## 💻 Installation locale

### Méthode 1 : Avec Git

#### 1. Cloner le projet
```bash
cd C:\xampp\htdocs  # Windows
# ou
cd /var/www/html    # Linux

git clone https://github.com/votre-username/skillafrik.git
cd skillafrik
```

#### 2. Créer la base de données
```bash
# Se connecter à MySQL
mysql -u root -p

# Créer la base
CREATE DATABASE skillafrik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 3. Importer le schéma
```bash
mysql -u root -p skillafrik < database/skillafrik_complete.sql
```

#### 4. Configurer la connexion
```bash
# Copier le fichier de configuration
cp config/database.example.php config/database.php

# Éditer avec vos paramètres
nano config/database.php  # Linux
notepad config/database.php  # Windows
```

Contenu de `config/database.php` :
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Votre mot de passe MySQL
define('DB_NAME', 'skillafrik');

function db_connect() {
    $conn = mysqli_init();
    mysqli_options($conn, MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");
    mysqli_options($conn, MYSQLI_SET_CHARSET_NAME, 'utf8');
    
    if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
        die('Erreur de connexion : ' . mysqli_connect_error());
    }
    
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_query($conn, "SET CHARACTER SET utf8");
    
    return $conn;
}
?>
```

#### 5. Configurer les permissions
```bash
# Linux/Mac
chmod 755 -R .
chmod 777 -R uploads/
chmod 777 -R storage/

# Windows (PowerShell en administrateur)
icacls uploads /grant Everyone:F /T
icacls storage /grant Everyone:F /T
```

#### 6. Accéder à l'application
```
http://localhost/skillafrik
```

### Méthode 2 : Sans Git (ZIP)

#### 1. Télécharger le projet
- Téléchargez le ZIP depuis GitHub
- Extrayez dans `C:\xampp\htdocs\skillafrik` (Windows) ou `/var/www/html/skillafrik` (Linux)

#### 2. Suivre les étapes 2 à 6 de la Méthode 1

---

## 🌐 Installation sur serveur

### Prérequis serveur

- Accès SSH
- Accès à un serveur web (Apache/Nginx)
- Accès à MySQL
- Nom de domaine (optionnel)

### Installation sur VPS (Ubuntu)

#### 1. Installer LAMP Stack
```bash
# Mettre à jour le système
sudo apt-get update
sudo apt-get upgrade

# Installer Apache
sudo apt-get install apache2

# Installer MySQL
sudo apt-get install mysql-server
sudo mysql_secure_installation

# Installer PHP
sudo apt-get install php libapache2-mod-php php-mysql php-mbstring php-gd php-curl

# Activer mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 2. Configurer le Virtual Host
```bash
sudo nano /etc/apache2/sites-available/skillafrik.conf
```

Contenu :
```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    ServerAlias www.votre-domaine.com
    DocumentRoot /var/www/skillafrik
    
    <Directory /var/www/skillafrik>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/skillafrik_error.log
    CustomLog ${APACHE_LOG_DIR}/skillafrik_access.log combined
</VirtualHost>
```

Activer le site :
```bash
sudo a2ensite skillafrik.conf
sudo systemctl reload apache2
```

#### 3. Cloner et configurer
```bash
cd /var/www
sudo git clone https://github.com/votre-username/skillafrik.git
cd skillafrik

# Permissions
sudo chown -R www-data:www-data .
sudo chmod 755 -R .
sudo chmod 777 -R uploads/
sudo chmod 777 -R storage/
```

#### 4. Base de données
```bash
sudo mysql -u root -p

CREATE DATABASE skillafrik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'skillafrik_user'@'localhost' IDENTIFIED BY 'mot_de_passe_fort';
GRANT ALL PRIVILEGES ON skillafrik.* TO 'skillafrik_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Importer
mysql -u skillafrik_user -p skillafrik < database/skillafrik_complete.sql
```

#### 5. Configurer l'application
```bash
cp config/database.example.php config/database.php
nano config/database.php
```

Modifier avec les identifiants créés.

#### 6. Configurer SSL (Let's Encrypt)
```bash
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d votre-domaine.com -d www.votre-domaine.com
```

#### 7. Configurer le pare-feu
```bash
sudo ufw allow 'Apache Full'
sudo ufw enable
```

---

## ⚙️ Configuration

### Configuration de base

#### 1. Fichier `.htaccess` (déjà inclus)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Sécurité
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

#### 2. Configuration PHP (`php.ini`)
```ini
# Augmenter les limites
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M

# Sécurité
display_errors = Off  # En production
log_errors = On
error_log = /var/log/php_errors.log

# Sessions
session.cookie_httponly = 1
session.cookie_secure = 1  # Si HTTPS
session.use_strict_mode = 1
```

#### 3. Configuration MySQL
```sql
# Optimisation
SET GLOBAL max_connections = 200;
SET GLOBAL innodb_buffer_pool_size = 256M;
```

### Configuration avancée

#### Variables d'environnement (optionnel)

Créer `.env` à la racine :
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_HOST=localhost
DB_NAME=skillafrik
DB_USER=skillafrik_user
DB_PASS=mot_de_passe_fort

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=votre-email@gmail.com
MAIL_PASS=mot_de_passe_app
MAIL_FROM=noreply@skillafrik.com
```

#### Configuration du mail

Créer `config/mail.php` :
```php
<?php
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'votre-email@gmail.com');
define('MAIL_PASS', 'mot_de_passe_app');
define('MAIL_FROM', 'noreply@skillafrik.com');
define('MAIL_FROM_NAME', 'SkillAfrik');
?>
```

---

## ✅ Vérification

### Checklist post-installation

#### 1. Tester la connexion à la base de données
```php
// test_db.php
<?php
require 'config/database.php';
$conn = db_connect();
if ($conn) {
    echo "✅ Connexion réussie !";
} else {
    echo "❌ Erreur de connexion";
}
?>
```

#### 2. Vérifier les permissions
```bash
# Linux
ls -la uploads/
ls -la storage/

# Doit afficher drwxrwxrwx ou 777
```

#### 3. Tester l'accès
- **Page d'accueil** : `http://localhost/skillafrik`
- **Connexion** : `http://localhost/skillafrik/login.php`
- **Admin** : `http://localhost/skillafrik/admin`

#### 4. Connexion test
**Admin :**
- Email : `admin@skillafrik.com`
- Mot de passe : `admin123`

**Utilisateur :**
- Email : `user@example.com`
- Mot de passe : `password123`

⚠️ **Changez ces mots de passe immédiatement !**

#### 5. Tester les fonctionnalités
- [ ] Inscription d'un nouvel utilisateur
- [ ] Connexion/Déconnexion
- [ ] Navigation des cours
- [ ] Inscription à un cours
- [ ] Suivi d'une leçon
- [ ] Passage d'un quiz
- [ ] Accès admin
- [ ] Création d'un cours
- [ ] Upload d'une image

---

## 🔧 Dépannage

### Problèmes courants

#### 1. Erreur "Call to undefined function db_connect()"

**Cause** : Le fichier `config/database.php` n'est pas inclus.

**Solution** :
```php
// Ajouter en haut du fichier
require_once 'config/database.php';
```

#### 2. Erreur "Access denied for user"

**Cause** : Identifiants MySQL incorrects.

**Solution** :
- Vérifier `config/database.php`
- Tester la connexion MySQL :
```bash
mysql -u root -p
```

#### 3. Page blanche

**Cause** : Erreur PHP non affichée.

**Solution** :
```php
// Ajouter temporairement en haut du fichier
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### 4. Images ne s'affichent pas

**Cause** : Permissions incorrectes.

**Solution** :
```bash
chmod 777 -R uploads/
```

#### 5. Erreur 404 sur les liens

**Cause** : `mod_rewrite` non activé.

**Solution** :
```bash
# Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Nginx
# Ajouter dans la config :
try_files $uri $uri/ /index.php?$query_string;
```

#### 6. Session expirée trop rapidement

**Cause** : Configuration session PHP.

**Solution** :
```ini
# php.ini
session.gc_maxlifetime = 3600
session.cookie_lifetime = 3600
```

#### 7. Upload échoue

**Cause** : Limite de taille.

**Solution** :
```ini
# php.ini
upload_max_filesize = 20M
post_max_size = 20M
```

### Logs utiles

#### Apache
```bash
# Erreurs
tail -f /var/log/apache2/error.log

# Accès
tail -f /var/log/apache2/access.log
```

#### PHP
```bash
tail -f /var/log/php_errors.log
```

#### MySQL
```bash
tail -f /var/log/mysql/error.log
```

---

## 🔒 Sécurité post-installation

### Checklist de sécurité

- [ ] Changer tous les mots de passe par défaut
- [ ] Configurer HTTPS (SSL)
- [ ] Désactiver `display_errors` en production
- [ ] Configurer les sauvegardes automatiques
- [ ] Limiter les tentatives de connexion
- [ ] Configurer un WAF
- [ ] Mettre à jour régulièrement
- [ ] Surveiller les logs

### Commandes utiles

```bash
# Sauvegarde base de données
mysqldump -u root -p skillafrik > backup_$(date +%Y%m%d).sql

# Sauvegarde fichiers
tar -czf backup_files_$(date +%Y%m%d).tar.gz /var/www/skillafrik

# Restauration
mysql -u root -p skillafrik < backup_20250106.sql
```

---

## 📞 Support

Si vous rencontrez des problèmes :

1. Consultez la section [Dépannage](#dépannage)
2. Vérifiez les logs d'erreurs
3. Créez un ticket sur [GitHub Issues](https://github.com/votre-username/skillafrik/issues)
4. Contactez-nous :
   - Email: manduarajonathan.m@gmail.com
   - Téléphone: +243890868095

---

**Installation réussie ? Passez au [Guide d'utilisation](USAGE.md) !**

---

**Créateur & Propriétaire** : Jonathan Manduara Tshimpaka  
**Contact** : manduarajonathan.m@gmail.com | +243890868095
