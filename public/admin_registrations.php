<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = isset($_POST['application_id']) ? htmlspecialchars(trim($_POST['application_id'])) : '';
    $action = isset($_POST['action']) ? htmlspecialchars(trim($_POST['action'])) : '';

    if (empty($application_id) || empty($action)) {
        $message = "Invalid action. Application ID and action must be provided.";
        $message_type = 'error';
    } elseif ($action === 'approve' || $action === 'reject') {
        $status = ($action === 'approve') ? 'approved' : 'rejected';

        $update_sql = "UPDATE users SET registration_status = ? WHERE utmid = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        if ($update_stmt === false) {
            error_log("Failed to prepare update statement for application: " . mysqli_error($conn));
            $message = "An internal server error occurred while processing application.";
            $message_type = 'error';
        } else {
            mysqli_stmt_bind_param($update_stmt, "ss", $status, $application_id);
            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Application successfully " . $status . ".";
                $message_type = 'success';
            } else {
                error_log("Error executing update statement for application: " . mysqli_stmt_error($update_stmt));
                $message = "Failed to update application status.";
                $message_type = 'error';
            }
            mysqli_stmt_close($update_stmt);
        }
    } else {
        $message = "Invalid action specified.";
        $message_type = 'error';
    }
}

$pending_applications = [];
$sql = "SELECT utmid, username, email, program_name, registration_status, phone_number FROM users WHERE role = 'student' AND registration_status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Failed to prepare pending applications statement: " . mysqli_error($conn));
    $applications_error = "Error fetching pending applications. Please try again.";
} else {
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pending_applications[] = array_map('htmlspecialchars', $row);
            }
        } else {
            error_log("Error getting pending applications result: " . mysqli_stmt_error($stmt));
            $applications_error = "Error fetching pending applications. Please try again.";
        }
    } else {
        error_log("Error executing pending applications statement: " . mysqli_stmt_error($stmt));
        $applications_error = "Error fetching pending applications. Please try again
