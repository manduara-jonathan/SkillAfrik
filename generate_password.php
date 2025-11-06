<?php
/**
 * Script de génération de hash de mot de passe
 * Auteur: Jonathan Manduara Tshimpaka
 * Email: manduarajonathan.m@gmail.com
 */

// Mot de passe à hasher
$password = 'jojoA2@19';

// Générer le hash bcrypt
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Mot de passe: $password\n";
echo "Hash bcrypt: $hash\n";
echo "\n";
echo "Pour vérifier: " . (password_verify($password, $hash) ? "✓ Valide" : "✗ Invalide") . "\n";
?>
