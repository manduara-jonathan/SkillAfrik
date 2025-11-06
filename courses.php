<?php
session_start();
require 'config/database.php';

$conn = db_connect();
$result = $conn->query('SELECT id, title, description, image_url FROM courses ORDER BY created_at DESC');
$courses = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

$pageTitle = "Tous les cours - SkillAfrik";
include 'views/partials/header.php';
include 'views/courses.php';
include 'views/partials/footer.php';
