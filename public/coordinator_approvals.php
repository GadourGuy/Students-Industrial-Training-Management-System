<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: ../index.html");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = isset($_POST['enrollment_id']) ? htmlspecialchars(trim($_POST['enrollment_id'])) : '';
    $action = isset($_POST['action']) ? htmlspecialchars(trim($_POST['action'])) : '';
    $student_utmid = isset($_POST['student_utmid']) ? htmlspecialchars(trim($_POST['student_utmid'])) : '';

    if (empty($enrollment_id) || empty($action) || empty($student_utmid)) {
        $message = "Invalid action. Enrollment ID, Student UTMID, and action must be provided.";
        $message_type = 'error';
    } elseif ($action === 'approve' || $action === 'reject') {
        $status = ($action === 'approve') ? 'approved' : 'rejected';

        $conn->begin_transaction();

        try {
            $update_enrollment_sql = "UPDATE enrollment SET status = ? WHERE enrollment_id = ?";
            $update_enrollment_stmt = mysqli_prepare($conn, $update_enrollment_sql);

            if ($update_enrollment_stmt === false) {
                throw new Exception("Failed to prepare update enrollment statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($update_enrollment_stmt, "si", $status, $enrollment_id);
            if (!mysqli_stmt_execute($update_enrollment_stmt)) {
                throw new Exception("Error executing update enrollment statement: " . mysqli_stmt_error($update_enrollment_stmt));
            }
            mysqli_stmt_close($update_enrollment_stmt);

            $update_user_sql = "UPDATE users SET registration_status = ? WHERE utmid = ?";
            $update_user_stmt = mysqli_prepare($conn, $update_user_sql);

            if ($update_user_stmt === false) {
                throw new Exception("Failed to prepare update user status statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($update_user_stmt, "
