-- ============================================================================
-- BASE DE DONNÉES COMPLÈTE - SKILLAFRIK
-- Version: 1.0 - Complète et Optimisée
-- Date: 6 Novembre 2025
-- Auteur: Jonathan Manduara Tshimpaka
-- Contact: manduarajonathan.m@gmail.com | +243890868095
-- Description: Script complet de création de la base de données SkillAfrik
-- ============================================================================

-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS skillafrik_db 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE skillafrik_db;

-- ============================================================================
-- SUPPRESSION DES TABLES EXISTANTES (si nécessaire)
-- ============================================================================
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badges;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS course_ratings;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS quiz_attempts;
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS lesson_completions;
DROP TABLE IF EXISTS lessons;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS difficulty_levels;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- TABLE: users (Utilisateurs)
-- ============================================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    bio TEXT,
    role ENUM('apprenant','formateur','admin','superadmin') NOT NULL DEFAULT 'apprenant',
    email_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: categories (Catégories de cours)
-- ============================================================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: difficulty_levels (Niveaux de difficulté)
-- ============================================================================
CREATE TABLE difficulty_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: courses (Cours)
-- ============================================================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT,
    difficulty_level_id INT,
    image_url VARCHAR(255),
    duration_hours INT,
    is_published TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_created_by (created_by),
    INDEX idx_category (category_id),
    INDEX idx_difficulty (difficulty_level_id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (difficulty_level_id) REFERENCES difficulty_levels(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: enrollments (Inscriptions aux cours)
-- ============================================================================
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress TINYINT UNSIGNED DEFAULT 0,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_course (user_id, course_id),
    INDEX idx_user (user_id),
    INDEX idx_course (course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: modules (Modules de cours)
-- ============================================================================
CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    `order` INT DEFAULT 0,
    INDEX idx_course (course_id),
    INDEX idx_order (course_id, `order`),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: lessons (Leçons)
-- ============================================================================
CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_type ENUM('text','video','quiz') NOT NULL DEFAULT 'text',
    content TEXT,
    duration_minutes INT DEFAULT 0,
    `order` INT DEFAULT 0,
    INDEX idx_module (module_id),
    INDEX idx_order (module_id, `order`),
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: lesson_completions (Suivi de progression)
-- ============================================================================
CREATE TABLE lesson_completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_lesson (user_id, lesson_id),
    INDEX idx_user (user_id),
    INDEX idx_lesson (lesson_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: quizzes (Quiz)
-- ============================================================================
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    INDEX idx_lesson (lesson_id),
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: questions (Questions de quiz)
-- ============================================================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    INDEX idx_quiz (quiz_id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: answers (Réponses aux questions)
-- ============================================================================
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    INDEX idx_question (question_id),
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: quiz_attempts (Tentatives de quiz)
-- ============================================================================
CREATE TABLE quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_quiz (quiz_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: course_ratings (Évaluations de cours)
-- ============================================================================
CREATE TABLE course_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_course_rating (user_id, course_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: comments (Commentaires sur les cours)
-- ============================================================================
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_course (course_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: password_reset_tokens (Tokens de réinitialisation)
-- ============================================================================
CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: notifications (Notifications utilisateurs)
-- ============================================================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','success','warning','error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: badges (Badges/Achievements)
-- ============================================================================
CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    criteria TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE: user_badges (Badges obtenus par les utilisateurs)
-- ============================================================================
CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- DONNÉES INITIALES
-- ============================================================================

-- Catégories
INSERT INTO categories (name, description, icon) VALUES
('Développement Web', 'Cours sur le développement web frontend et backend', 'code'),
('Culture Numérique', 'Compétences numériques de base', 'computer'),
('Cybersécurité', 'Sécurité informatique et protection des données', 'shield'),
('Design', 'Design graphique et UX/UI', 'palette'),
('Data Science', 'Analyse de données et intelligence artificielle', 'chart-bar'),
('Marketing Digital', 'Marketing en ligne et réseaux sociaux', 'megaphone');

-- Niveaux de difficulté
INSERT INTO difficulty_levels (name, description) VALUES
('Débutant', 'Aucune connaissance préalable requise'),
('Intermédiaire', 'Connaissances de base requises'),
('Avancé', 'Expérience significative requise'),
('Expert', 'Niveau professionnel');

-- Badges
INSERT INTO badges (name, description, icon, criteria) VALUES
('Premier Pas', 'Complété votre première leçon', 'star', 'Complete 1 lesson'),
('Apprenant Assidu', 'Complété 10 leçons', 'trophy', 'Complete 10 lessons'),
('Maître du Quiz', 'Obtenu 100% à un quiz', 'award', 'Score 100% on a quiz'),
('Diplômé', 'Complété votre premier cours', 'graduation-cap', 'Complete 1 course'),
('Collectionneur', 'Complété 5 cours', 'medal', 'Complete 5 courses');

-- Super Administrateur (mot de passe: jojoA2@19)
-- Jonathan Manduara Tshimpaka - Créateur et Propriétaire de SkillAfrik
-- Contact: manduarajonathan.m@gmail.com | +243890868095
INSERT INTO users (username, first_name, last_name, email, password, phone, role, email_verified, bio) VALUES
('jonathan_manduara', 'Jonathan', 'Manduara Tshimpaka', 'manduarajonathan.m@gmail.com', '$2y$12$2aDygxHIFzWHcOdj/TTZVuDi0VSTgKdrx.Q7.JW5yrCR5J0fqtMKe', '+243890868095', 'superadmin', 1, 'Créateur et Propriétaire de SkillAfrik - Plateforme d''éducation numérique pour l''Afrique');

-- Utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO users (username, first_name, last_name, email, password, role, email_verified) VALUES
('admin', 'Admin', 'SkillAfrik', 'admin@skillafrik.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);

-- Cours d'exemple
INSERT INTO courses (id, title, description, category_id, difficulty_level_id, image_url, duration_hours, created_by) VALUES
(1, 'Développement Web Niveau Intermédiaire', 'Apprenez à construire des applications web dynamiques avec PHP et JavaScript. Ce cours couvre les bases de données, la gestion des sessions, et les APIs REST.', 1, 2, 'https://via.placeholder.com/400x225/AEC6CF/FFFFFF?text=Dev+Web', 40, 1),
(2, 'Culture Numérique pour Tous', 'Découvrez les concepts fondamentaux du monde numérique, de la sécurité en ligne à l''utilisation efficace des outils de bureautique.', 2, 1, 'https://via.placeholder.com/400x225/FFDDDD/808080?text=Culture+Numérique', 20, 1),
(3, 'Introduction à la Cybersécurité', 'Protégez-vous et protégez les autres des menaces en ligne. Ce cours vous initie aux principes de la cybersécurité.', 3, 2, 'https://via.placeholder.com/400x225/DDDDFF/808080?text=Cybersécurité', 30, 1);

-- Modules d'exemple
INSERT INTO modules (course_id, title, `order`) VALUES
(1, 'Introduction à PHP', 1),
(1, 'Interagir avec une base de données MySQL', 2),
(1, 'Gestion des sessions et des cookies', 3),
(2, 'Les bases de la navigation sécurisée', 1),
(2, 'Utiliser efficacement une suite bureautique', 2);

-- Leçons d'exemple
INSERT INTO lessons (module_id, title, content_type, content, duration_minutes, `order`) VALUES
(1, 'Variables et types de données', 'text', 'Le PHP supporte plusieurs types de données : entiers, flottants, chaînes de caractères, booléens, tableaux et objets. Les variables en PHP commencent toujours par le symbole $.', 30, 1),
(1, 'Structures de contrôle', 'text', 'Les structures de contrôle comme if/else et les boucles (for, while, foreach) permettent de contrôler le flux d''exécution de votre code.', 45, 2),
(2, 'Connexion à la base de données', 'video', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 25, 1),
(2, 'Exécuter des requêtes préparées', 'text', 'Les requêtes préparées sont essentielles pour la sécurité de votre application. Elles protègent contre les injections SQL.', 40, 2);

-- ============================================================================
-- VUES UTILES
-- ============================================================================

-- Vue pour les statistiques des cours
CREATE OR REPLACE VIEW course_statistics AS
SELECT 
    c.id,
    c.title,
    cat.name as category_name,
    dl.name as difficulty_level,
    COUNT(DISTINCT e.user_id) as total_enrollments,
    COUNT(DISTINCT m.id) as total_modules,
    COUNT(DISTINCT l.id) as total_lessons,
    COALESCE(AVG(cr.rating), 0) as average_rating,
    COUNT(DISTINCT cr.id) as total_ratings
FROM courses c
LEFT JOIN categories cat ON c.category_id = cat.id
LEFT JOIN difficulty_levels dl ON c.difficulty_level_id = dl.id
LEFT JOIN enrollments e ON c.id = e.course_id
LEFT JOIN modules m ON c.id = m.course_id
LEFT JOIN lessons l ON m.id = l.module_id
LEFT JOIN course_ratings cr ON c.id = cr.course_id
GROUP BY c.id, c.title, cat.name, dl.name;

-- Vue pour la progression des utilisateurs
CREATE OR REPLACE VIEW user_progress AS
SELECT 
    u.id as user_id,
    u.username,
    c.id as course_id,
    c.title as course_title,
    COUNT(DISTINCT l.id) as total_lessons,
    COUNT(DISTINCT lc.lesson_id) as completed_lessons,
    ROUND((COUNT(DISTINCT lc.lesson_id) / NULLIF(COUNT(DISTINCT l.id), 0)) * 100, 2) as progress_percentage
FROM users u
JOIN enrollments e ON u.id = e.user_id
JOIN courses c ON e.course_id = c.id
JOIN modules m ON c.id = m.course_id
JOIN lessons l ON m.id = l.module_id
LEFT JOIN lesson_completions lc ON l.id = lc.lesson_id AND u.id = lc.user_id
GROUP BY u.id, u.username, c.id, c.title;

-- ============================================================================
-- PROCÉDURES STOCKÉES
-- ============================================================================

DELIMITER //

-- Procédure pour calculer la progression d'un utilisateur dans un cours
CREATE PROCEDURE IF NOT EXISTS calculate_user_course_progress(
    IN p_user_id INT,
    IN p_course_id INT,
    OUT p_progress INT
)
BEGIN
    DECLARE total_lessons INT;
    DECLARE completed_lessons INT;
    
    -- Compter le total de leçons
    SELECT COUNT(l.id) INTO total_lessons
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE m.course_id = p_course_id;
    
    -- Compter les leçons complétées
    SELECT COUNT(DISTINCT lc.lesson_id) INTO completed_lessons
    FROM lesson_completions lc
    JOIN lessons l ON lc.lesson_id = l.id
    JOIN modules m ON l.module_id = m.id
    WHERE lc.user_id = p_user_id AND m.course_id = p_course_id;
    
    -- Calculer le pourcentage
    IF total_lessons > 0 THEN
        SET p_progress = ROUND((completed_lessons / total_lessons) * 100);
    ELSE
        SET p_progress = 0;
    END IF;
END //

DELIMITER ;

-- ============================================================================
-- TRIGGERS
-- ============================================================================

DELIMITER //

-- Trigger pour mettre à jour la progression après complétion d'une leçon
CREATE TRIGGER IF NOT EXISTS after_lesson_completion
AFTER INSERT ON lesson_completions
FOR EACH ROW
BEGIN
    DECLARE course_id INT;
    DECLARE progress_value INT;
    
    -- Trouver le cours associé
    SELECT m.course_id INTO course_id
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE l.id = NEW.lesson_id;
    
    -- Calculer la progression
    CALL calculate_user_course_progress(NEW.user_id, course_id, progress_value);
    
    -- Mettre à jour la table enrollments
    UPDATE enrollments
    SET progress = progress_value
    WHERE user_id = NEW.user_id AND course_id = course_id;
END //

DELIMITER ;

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================

-- Afficher un message de succès
SELECT 'Base de données SkillAfrik créée avec succès!' as Message;
SELECT COUNT(*) as 'Nombre de tables créées' FROM information_schema.tables WHERE table_schema = 'skillafrik_db';
