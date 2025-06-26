<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.html");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utmid = $_SESSION['utmid'];
    $program_name = isset($_POST['program_name']) ? htmlspecialchars(trim($_POST['program_name'])) : '';
    $duration = isset($_POST['duration']) ? htmlspecialchars(trim($_POST['duration'])) : '';
    $start_date = isset($_POST['start_date']) ? htmlspecialchars(trim($_POST['start_date'])) : '';
    $end_date = isset($_POST['end_date']) ? htmlspecialchars(trim($_POST['end_date'])) : '';
    $company_name = isset($_POST['company_name']) ? htmlspecialchars(trim($_POST['company_name'])) : '';
    $company_address = isset($_POST['company_address']) ? htmlspecialchars(trim($_POST['company_address'])) : '';
    $company_supervisor = isset($_POST['company_supervisor']) ? htmlspecialchars(trim($_POST['company_supervisor'])) : '';
    $supervisor_contact = isset($_POST['supervisor_contact']) ? htmlspecialchars(trim($_POST['supervisor_contact'])) : '';
    $internship_type = isset($_POST['internship_type']) ? htmlspecialchars(trim($_POST['internship_type'])) : '';

    if (empty($program_name) || empty($duration) || empty($start_date) || empty($end_date) || empty($company_name) || empty($company_address) || empty($company_supervisor) || empty($supervisor_contact) || empty($internship_type)) {
        $_SESSION['enrollment_message'] = "All fields are required.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }

    $sql_check = "SELECT enrollment_id FROM enrollment WHERE utmid = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    if ($stmt_check === false) {
        error_log("Failed to prepare enrollment check statement: " . mysqli_error($conn));
        $_SESSION['enrollment_message'] = "An internal server error occurred.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }
    mysqli_stmt_bind_param($stmt_check, "s", $utmid);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['enrollment_message'] = "You have already submitted an enrollment. Please contact your coordinator for changes.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }
    mysqli_stmt_close($stmt_check);

    $sql_insert = "INSERT INTO enrollment (utmid, program_name, duration, start_date, end_date, company_name, company_address, company_supervisor, supervisor_contact, internship_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    if ($stmt_insert === false) {
        error_log("Failed to prepare enrollment insert statement: " . mysqli_error($conn));
        $_SESSION['enrollment_message'] = "An internal server error occurred. Please try again later.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }

    mysqli_stmt_bind_param($stmt_insert, "ssssssssss", $utmid, $program_name, $duration, $start_date, $end_date, $company_name, $company_address, $company_supervisor, $supervisor_contact, $internship_type);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['enrollment_message'] = "Enrollment submitted successfully! It is now pending approval.";
        $_SESSION['message_type'] = 'success';
        header("Location: ../public/studentEnrollmentCenter.html"); // Redirect to a success page or back to the form with success message
        exit();
    } else {
        error_log("Enrollment submission failed: " . mysqli_stmt_error($stmt_insert));
        $_SESSION['enrollment_message'] = "Error submitting enrollment. Please try again.";
        $_SESSION['message_type'] = 'error';
        header("Location: ../public/studentEnrollmentCenter.html");
        exit();
    }

    mysqli_stmt_close($stmt_insert);
} else {
    header("Location: ../public/studentEnrollmentCenter.html");
    exit();
}
mysqli_close($conn);
?>
