<?php
require 'db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'Invalid student selected.');
    header('Location: students.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT name FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $studentName);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$found) {
    setFlash('error', 'Student not found.');
    header('Location: students.php');
    exit;
}

// Deleting the student also removes the related marks row (ON DELETE CASCADE)
$deleteStmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
mysqli_stmt_bind_param($deleteStmt, 'i', $id);

if (mysqli_stmt_execute($deleteStmt)) {
    setFlash('success', 'Student "' . $studentName . '" and related marks deleted successfully.');
} else {
    setFlash('error', 'Could not delete student. Please try again.');
}
mysqli_stmt_close($deleteStmt);

header('Location: students.php');
exit;
