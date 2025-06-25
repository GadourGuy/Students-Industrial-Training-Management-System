<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$message = '';
$message_type = '';
$student_data = null;

if (isset($_GET['utmid'])) {
    $utmid_to_fetch = htmlspecialchars(trim($_GET['utmid']));
    $sql_fetch = "SELECT utmid, username, email, phone_number, program_name, registration_status FROM users WHERE utmid = ? AND role = 'student'";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch);

    if ($stmt_fetch === false) {
        error_log("Failed to prepare fetch student statement: " . mysqli_error($conn));
        $message = "Error retrieving student data.";
        $message_type = 'error';
    } else {
        mysqli_stmt_bind_param($stmt_fetch, "s", $utmid_to_fetch);
        if (mysqli_stmt_execute($stmt_fetch)) {
            $result_fetch = mysqli_stmt_get_result($stmt_fetch);
            if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
                $student_data = mysqli_fetch_assoc($result_fetch);
                $student_data = array_map('htmlspecialchars', $student_data);
            } else {
                $message = "Student not found.";
                $message_type = 'error';
            }
        } else {
            error_log("Error executing fetch student statement: " . mysqli_stmt_error($stmt_fetch));
            $message = "Error retrieving student data.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt_fetch);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $utmid_to_update = isset($_POST['utmid']) ? htmlspecialchars(trim($_POST['utmid'])) : '';
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
    $program_name = isset($_POST['program_name']) ? htmlspecialchars(trim($_POST['program_name'])) : '';
    $registration_status = isset($_POST['registration_status']) ? htmlspecialchars(trim($_POST['registration_status'])) : '';

    if (empty($utmid_to_update) || empty($username) || empty($email) || empty($phone_number) || empty($program_name) || empty($registration_status)) {
        $message = "All fields are required for update.";
        $message_
