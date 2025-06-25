<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drop_program'])) {
    $utmid = $_SESSION['utmid'];
    $enrollment_id = isset($_POST['enrollment_id']) ? htmlspecialchars(trim($_POST['enrollment_id'])) : '';

    if (empty($enrollment_id)) {
        $_SESSION['enrollment_message'] = "Invalid request. Enrollment ID is missing.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }

    $conn->begin_transaction();

    try {
        $sql_delete_enrollment = "DELETE FROM enrollment WHERE enrollment_id = ? AND utmid = ?";
        $stmt_delete_enrollment = mysqli_prepare($conn, $sql_delete_enrollment);

        if ($stmt_delete_enrollment === false) {
            throw new Exception("Failed to prepare delete enrollment statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_delete_enrollment, "is", $enrollment_id, $utmid);
        if (!mysqli_stmt_execute($stmt_delete_enrollment)) {
            throw new Exception("Error executing delete enrollment statement: " . mysqli_stmt_error($stmt_delete_enrollment));
        }
        $affected_rows = mysqli_stmt_affected_rows($stmt_delete_enrollment);
        mysqli_stmt_close($stmt_delete_enrollment);

        if ($affected_rows === 0) {
            throw new Exception("Enrollment record not found or you don't have permission to delete it.");
        }

        $sql_update_user_status = "UPDATE users SET program_name = NULL, registration_status = 'pending' WHERE utmid = ?";
        $stmt_update_user_status = mysqli_prepare($conn, $sql_update_user_status);

        if ($stmt_update_user_status === false) {
            throw new Exception("Failed to prepare update user status statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_update_user_status, "s", $utmid);
        if (!mysqli_stmt_execute($stmt_update_user_status)) {
            throw new Exception("Error executing update user status statement: " . mysqli
