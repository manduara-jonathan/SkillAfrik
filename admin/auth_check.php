<?php
/**
 * Vérification d'authentification pour l'administration
 * 
 * Auteur: Jonathan Manduara Tshimpaka
 * Email: manduarajonathan.m@gmail.com
 * Téléphone: +243890868095
 */

session_start();

// Vérifie si l'utilisateur est connecté et a le bon rôle (superadmin, admin ou formateur)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['superadmin', 'admin', 'formateur'])) {
    // Si non, on le redirige vers la page de connexion avec un message d'erreur
    $_SESSION['error_message'] = "Accès refusé. Vous n'avez pas les droits nécessaires.";
    header('Location: ../login.php');
    exit();
}
