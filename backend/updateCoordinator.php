<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$message = '';
$message_type = '';
$coordinator_data = null;

if (isset($_GET['utmid'])) {
    $utmid_to_fetch = htmlspecialchars(trim($_GET['utmid']));
    $sql_fetch = "SELECT utmid, username, email, phone_number, department FROM users WHERE utmid = ? AND role = 'coordinator'";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch);

    if ($stmt_fetch === false) {
        error_log("Failed to prepare fetch coordinator statement: " . mysqli_error($conn));
        $message = "Error retrieving coordinator data.";
        $message_type = 'error';
    } else {
        mysqli_stmt_bind_param($stmt_fetch, "s", $utmid_to_fetch);
        if (mysqli_stmt_execute($stmt_fetch)) {
            $result_fetch = mysqli_stmt_get_result($stmt_fetch);
            if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
                $coordinator_data = mysqli_fetch_assoc($result_fetch);
                $coordinator_data = array_map('htmlspecialchars', $coordinator_data);
            } else {
                $message = "Coordinator not found.";
                $message_type = 'error';
            }
        } else {
            error_log("Error executing fetch coordinator statement: " . mysqli_stmt_error($stmt_fetch));
            $message = "Error retrieving coordinator data.";
            $message_type = 'error';
        }
        mysqli_stmt_close($stmt_fetch);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_coordinator'])) {
    $utmid_to_update = isset($_POST['utmid']) ? htmlspecialchars(trim($_POST['utmid'])) : '';
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
    $department = isset($_POST['department']) ? htmlspecialchars(trim($_POST['department'])) : '';

    if (empty($utmid_to_update) || empty($username) || empty($email) || empty($phone_number) || empty($department)) {
        $message = "All fields are required for update.";
        $message_type = 'error';
    } else {
        $sql_update = "UPDATE users SET username = ?, email = ?, phone_number = ?, department = ? WHERE utmid = ? AND role = 'coordinator'";
        $stmt_update = mysqli_prepare($conn, $sql_update);

        if ($stmt_update === false) {
            error_log("Failed to prepare update coordinator statement: " . mysqli_error($conn));
            $message = "An internal server error occurred during update.";
            $message_type = 'error';
        } else {
            mysqli_stmt_bind_param($stmt_update, "sssss", $username, $email, $phone_number, $department, $utmid_to_update);
            if (mysqli_stmt_execute($stmt_update)) {
                $message = "Coordinator record updated successfully!";
                $message_type = 'success';
                $coordinator_data = array_map('htmlspecialchars', ['utmid' => $utmid_to_update, 'username' => $username, 'email' => $email, 'phone_number' => $phone_number, 'department' => $department]);
            } else {
                error_log("Error executing update coordinator statement: " . mysqli_stmt_error($stmt_
