<?php
require 'config/database.php';
require 'lib/fpdf.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['course_id'])) {
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = (int)$_GET['course_id'];

$conn = db_connect();

// Vérifier si l'utilisateur a bien terminé le cours
$stmt = $conn->prepare('SELECT 
    (SELECT COUNT(*) FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = ?) as total_lessons,
    (SELECT COUNT(*) FROM lesson_completions lc WHERE lc.user_id = ? AND lc.lesson_id IN (SELECT l.id FROM lessons l JOIN modules m ON l.module_id = m.id WHERE m.course_id = ?)) as completed_lessons
');
$stmt->bind_param('iii', $course_id, $user_id, $course_id);
$stmt->execute();
$progress_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($progress_data['total_lessons'] == 0 || $progress_data['completed_lessons'] < $progress_data['total_lessons']) {
    // L'utilisateur n'a pas terminé le cours
    die('Vous n\'avez pas encore terminé ce cours.');
}

// Récupérer les noms
$course_title = $conn->query('SELECT title FROM courses WHERE id = ' . $course_id)->fetch_assoc()['title'];
$username = $_SESSION['username'];
$conn->close();

// Génération du PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Titre du certificat
$pdf->Cell(0, 20, 'CERTIFICAT DE REUSSITE', 0, 1, 'C');
$pdf->Ln(20);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Decerne a :', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 24);
$pdf->Cell(0, 20, utf8_decode($username), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Pour avoir complete avec succes le cours :', 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 20);
$pdf->Cell(0, 20, utf8_decode($course_title), 0, 1, 'C');

$pdf->SetY(-40);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Date : ' . date('d/m/Y'), 0, 1, 'L');
$pdf->Cell(0, 10, 'SkillAfrik Platform', 0, 1, 'R');

$pdf->Output('D', 'Certificat-' . str_replace(' ', '-', $course_title) . '.pdf');
