<?php
session_start();
require 'config/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

// Récupérer les données envoyées en JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['lesson_id']) || !filter_var($data['lesson_id'], FILTER_VALIDATE_INT)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'ID de leçon manquant ou invalide']);
    exit();
}

$user_id = $_SESSION['user_id'];
$lesson_id = (int)$data['lesson_id'];

$conn = db_connect();

// Insérer dans la table lesson_completions (ignore si déjà existant grâce à la clé unique)
$stmt = $conn->prepare('INSERT IGNORE INTO lesson_completions (user_id, lesson_id) VALUES (?, ?)');
$stmt->bind_param('ii', $user_id, $lesson_id);

if ($stmt->execute()) {
    // Mettre à jour la progression globale du cours (simplifié)
    // Une logique plus complexe calculerait le % exact
    echo json_encode(['status' => 'success', 'message' => 'Progression enregistrée']);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données']);
}

$stmt->close();
$conn->close();
