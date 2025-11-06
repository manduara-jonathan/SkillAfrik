<?php
require 'config/csrf.php';
require 'config/database.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        die('Invalide CSRF token');
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validation
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'email existe déjà
    $conn = db_connect();
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Cette adresse email est déjà utilisée.";
    }
    $stmt->close();

    // Si pas d'erreurs, insérer en BDD
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $password_hash);
        
        if ($stmt->execute()) {
            // Rediriger vers la page de connexion avec un message de succès
            $_SESSION['success_message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            header('Location: login.php');
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de l'inscription.";
        }
        $stmt->close();
    }
    $conn->close();
}

$pageTitle = "Inscription - SkillAfrik";
include 'views/partials/header.php';
include 'views/register.php';
include 'views/partials/footer.php';
