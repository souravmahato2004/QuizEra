<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../frontend/login.php");
    exit();
}

include 'db.php';

// Fetch user ID
$userId= $_SESSION['id'];

// === Delete related data first (important for foreign key constraints) ===
// Example tables: quiz_data, quiz_levels (change names as per your DB)
$deleteLevels = $conn->prepare("DELETE FROM quiz_sessions WHERE host_id = ?");
$deleteLevels->bind_param("i", $userId);
$deleteLevels->execute();
$deleteLevels->close();

$deleteQuizzes = $conn->prepare("DELETE FROM quizzes WHERE owner_id = ?");
$deleteQuizzes->bind_param("i", $userId);
$deleteQuizzes->execute();
$deleteQuizzes->close();

// Delete user record
$deleteUser = $conn->prepare("DELETE FROM user_info WHERE user_id = ?");
$deleteUser->bind_param("i", $userId);
$deleteUser->execute();
$deleteUser->close();

$conn->close();

// Clear session and redirect
session_unset();
session_destroy();
header("Location: ../"); // You can make this a farewell page
exit();
?>
