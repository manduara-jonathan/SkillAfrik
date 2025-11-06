# 📚 Documentation SkillAfrik

Bienvenue dans la documentation complète de SkillAfrik !

---

## 📖 Table des matières

### 🚀 Pour commencer
- **[README principal](../README.md)** - Vue d'ensemble du projet
- **[Guide d'installation](INSTALLATION.md)** - Installation pas à pas
- **[Changelog](../CHANGELOG.md)** - Historique des versions

### 🏗️ Documentation technique
- **[Architecture](ARCHITECTURE.md)** - Architecture technique détaillée
- **[Base de données](../database/skillafrik_complete.sql)** - Schéma SQL complet

### 🤝 Contribution
- **[Guide de contribution](CONTRIBUTING.md)** - Comment contribuer au projet
- **[Licence](../LICENSE)** - Licence MIT

---

## 📂 Structure de la documentation

```
docs/
├── README.md              # Ce fichier - Index de la documentation
├── INSTALLATION.md        # Guide d'installation complet
├── ARCHITECTURE.md        # Documentation de l'architecture
└── CONTRIBUTING.md        # Guide de contribution
```

---

## 🎯 Guides rapides

### Installation en 5 minutes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-username/skillafrik.git
cd skillafrik

# 2. Créer la base de données
mysql -u root -p -e "CREATE DATABASE skillafrik CHARACTER SET utf8mb4"
mysql -u root -p skillafrik < database/skillafrik_complete.sql

# 3. Configurer
cp config/database.example.php config/database.php
# Éditer config/database.php avec vos identifiants

# 4. Permissions
chmod 777 -R uploads/ storage/

# 5. Accéder
# http://localhost/skillafrik
```

### Connexion par défaut

**Administrateur :**
- Email : `admin@skillafrik.com`
- Mot de passe : `admin123`

⚠️ **Changez ce mot de passe immédiatement !**

---

## 🔍 Navigation rapide

### Par rôle

#### 👤 Utilisateur
- [S'inscrire et se connecter](INSTALLATION.md#vérification)
- [Parcourir les cours](../README.md#pour-les-utilisateurs)
- [Suivre un cours](../README.md#pour-les-utilisateurs)
- [Passer un quiz](../README.md#pour-les-utilisateurs)

#### 👨‍💼 Administrateur
- [Accéder au panel admin](../README.md#pour-les-administrateurs)
- [Gérer les cours](../README.md#pour-les-administrateurs)
- [Créer des modules](../README.md#pour-les-administrateurs)
- [Gérer les quiz](../README.md#pour-les-administrateurs)

#### 👨‍💻 Développeur
- [Architecture du projet](ARCHITECTURE.md)
- [Standards de code](CONTRIBUTING.md#standards-de-code)
- [Contribuer](CONTRIBUTING.md)
- [Signaler un bug](CONTRIBUTING.md#signaler-un-bug)

---

## 📊 Diagrammes

### Architecture globale

```
Client (Navigateur)
        ↓
Serveur Web (Apache)
        ↓
Application PHP (MVC)
        ↓
Base de données (MySQL)
```

### Structure des données

```
Cours
  └── Modules
        └── Leçons
              ├── Contenu (texte/vidéo)
              └── Quiz (optionnel)
                    └── Questions
                          └── Réponses
```

---

## 🔧 Configuration

### Prérequis minimaux

| Composant | Version |
|-----------|---------|
| PHP | 7.4+ |
| MySQL | 5.7+ |
| Apache | 2.4+ |
| Espace disque | 500 MB |
| RAM | 512 MB |

### Configuration recommandée

| Composant | Version |
|-----------|---------|
| PHP | 8.0+ |
| MySQL | 8.0+ |
| Apache | 2.4+ |
| Espace disque | 2 GB |
| RAM | 2 GB |

---

## 🔒 Sécurité

### Fonctionnalités de sécurité

- ✅ Protection CSRF
- ✅ Protection SQL Injection
- ✅ Protection XSS
- ✅ Hachage bcrypt
- ✅ Sessions sécurisées
- ✅ Validation des données
- ✅ Gestion des permissions

### Checklist de sécurité

Avant de déployer en production :

- [ ] Changer tous les mots de passe par défaut
- [ ] Activer HTTPS (SSL)
- [ ] Désactiver `display_errors`
- [ ] Configurer les sauvegardes
- [ ] Limiter les tentatives de connexion
- [ ] Configurer un WAF
- [ ] Mettre à jour régulièrement

---

## 📈 Performance

### Optimisations implémentées

- Index de base de données
- Requêtes SQL optimisées
- Fermeture des connexions
- Validation côté serveur

### Recommandations

- Utiliser un CDN pour les assets
- Activer la compression gzip
- Mettre en cache les requêtes fréquentes
- Optimiser les images

---

## 🐛 Dépannage

### Problèmes courants

#### Erreur "Call to undefined function"
```php
// Solution : Ajouter le require manquant
require_once 'config/database.php';
```

#### Erreur "Access denied"
```php
// Solution : Vérifier config/database.php
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

#### Page blanche
```php
// Solution : Activer l'affichage des erreurs temporairement
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Logs utiles

```bash
# Apache
tail -f /var/log/apache2/error.log

# PHP
tail -f /var/log/php_errors.log

# MySQL
tail -f /var/log/mysql/error.log
```

---

## 📞 Support

### Obtenir de l'aide

1. **Documentation** : Consultez cette documentation
2. **FAQ** : Vérifiez les questions fréquentes
3. **Issues GitHub** : [Créer un ticket](https://github.com/votre-username/skillafrik/issues)
4. **Email** : manduarajonathan.m@gmail.com
5. **Téléphone** : +243890868095
6. **Discord** : [Rejoindre la communauté](#)

### Temps de réponse

- Issues GitHub : 24-48h
- Email : 3-5 jours
- Discord : Temps réel (selon disponibilité)

---

## 🗺️ Roadmap

### Version 1.1 (Q1 2025)
- Notifications en temps réel
- Récupération de mot de passe
- Profil utilisateur éditable
- Badges et gamification

### Version 1.2 (Q2 2025)
- Application mobile
- API REST complète
- Système de paiement
- Marketplace de cours

### Version 2.0 (Q3 2025)
- Live streaming
- Visioconférence
- Forum communautaire
- Système de mentorat

---

## 📚 Ressources additionnelles

### Tutoriels
- [Créer son premier cours](#)
- [Personnaliser le design](#)
- [Ajouter une nouvelle fonctionnalité](#)

### Vidéos
- [Installation complète](#)
- [Tour d'horizon des fonctionnalités](#)
- [Guide administrateur](#)

### Articles de blog
- [Pourquoi SkillAfrik ?](#)
- [Architecture technique expliquée](#)
- [Meilleures pratiques](#)

---

## 🤝 Communauté

### Rejoignez-nous

- **GitHub** : [Star le projet](https://github.com/votre-username/skillafrik)
- **Discord** : [Rejoindre le serveur](#)
- **Twitter** : [@SkillAfrik](#)
- **LinkedIn** : [Page SkillAfrik](#)

### Contribuer

Nous accueillons toutes les contributions ! Consultez le [Guide de contribution](CONTRIBUTING.md).

---

## 📄 Licence

Ce projet est sous licence MIT. Voir [LICENSE](../LICENSE) pour plus de détails.

---

## 🙏 Remerciements

Merci à tous ceux qui ont contribué à faire de SkillAfrik une réalité :

- Tous les contributeurs
- La communauté open-source
- Les utilisateurs pour leurs retours

---

## 📊 Statistiques du projet

- **Lignes de code** : ~5,000+
- **Fichiers** : 50+
- **Tables BDD** : 10
- **Fonctionnalités** : 20+
- **Documentation** : 100% couverte

---

## 🎯 Objectifs

Notre mission : **Démocratiser l'accès à l'éducation numérique en Afrique**

### Nos valeurs

- 🌍 **Accessibilité** : Éducation pour tous
- 🔓 **Open Source** : Transparence et collaboration
- 🚀 **Innovation** : Technologies modernes
- 🤝 **Communauté** : Ensemble, nous sommes plus forts

---

**Développé avec ❤️ pour l'éducation numérique en Afrique**

**Créateur & Propriétaire** : Jonathan Manduara Tshimpaka  
**Contact** : manduarajonathan.m@gmail.com | +243890868095

**© 2025 SkillAfrik. Tous droits réservés.**

---

**Besoin d'aide ? Consultez notre [Guide d'installation](INSTALLATION.md) ou [contactez-nous](mailto:manduarajonathan.m@gmail.com) !**
