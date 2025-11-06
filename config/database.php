<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // A changer en production
define('DB_PASS', ''); // A changer en production
define('DB_NAME', 'skillafrik_db');

/**
 * Connexion à la base de données
 * @return mysqli|false
 */
function db_connect() {
    // Initialiser la connexion
    $conn = mysqli_init();
    if (!$conn) {
        die('mysqli_init a échoué');
    }

    // Définir le charset AVANT la connexion pour éviter les problèmes de charset inconnu
    mysqli_options($conn, MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");
    mysqli_options($conn, MYSQLI_SET_CHARSET_NAME, 'utf8');

    // Tenter de se connecter
    if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
        // En production, logguer l'erreur plutôt que de l'afficher
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }

    // Exécuter SET NAMES en tant que requête de secours
    mysqli_query($conn, "SET NAMES 'utf8'");
    mysqli_query($conn, "SET CHARACTER SET utf8");
    mysqli_query($conn, "SET character_set_connection=utf8");

    return $conn;
}
