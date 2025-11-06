<?php
/**
 * Vérification des permissions Super Administrateur
 * 
 * Ce fichier vérifie si l'utilisateur connecté est un super-administrateur
 * Le super-administrateur a tous les droits sur la plateforme
 * 
 * Auteur: Jonathan Manduara Tshimpaka
 * Email: manduarajonathan.m@gmail.com
 * Téléphone: +243890868095
 */

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Vérifier si l'utilisateur est un super-administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    // Rediriger vers le dashboard approprié selon le rôle
    if ($_SESSION['role'] === 'admin') {
        header('Location: index.php');
    } else {
        header('Location: ../dashboard.php');
    }
    exit();
}

// Si on arrive ici, l'utilisateur est un super-administrateur
// Il a accès à toutes les fonctionnalités
?>
