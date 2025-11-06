<?php
require 'config/csrf.php';
require 'config/database.php';

// Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord.
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        die('Invalide CSRF token');
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }

    if (empty($errors)) {
        $conn = db_connect();
        $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Le mot de passe est correct, on démarre la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['role'] = $user['role']; // Pour compatibilité
            
            // Mise à jour de la dernière connexion
            $update_stmt = $conn->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
            $update_stmt->bind_param('i', $user['id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Redirection selon le rôle
            if ($user['role'] === 'superadmin' || $user['role'] === 'admin' || $user['role'] === 'formateur') {
                header('Location: admin/index.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $errors[] = "Identifiants incorrects.";
        }
        $stmt->close();
        $conn->close();
    }
}

$pageTitle = "Connexion - SkillAfrik";
include 'views/partials/header.php';
include 'views/login.php';
include 'views/partials/footer.php';
