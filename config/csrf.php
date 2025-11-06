<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère et stocke un jeton CSRF dans la session.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        // Alternative compatible avec PHP < 7.0
        if (function_exists('random_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            // Fallback pour les très anciennes versions de PHP
            $_SESSION['csrf_token'] = bin2hex(uniqid(mt_rand(), true));
        }
    }
}

/**
 * Valide le jeton CSRF soumis.
 * @return bool
 */
function validate_csrf_token() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Affiche le champ de formulaire caché avec le jeton CSRF.
 */
function csrf_input() {
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

// Générer un jeton pour chaque chargement de page qui contient un formulaire
generate_csrf_token();
