<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: ../index.html");
    exit();
}

$search_query = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$filter_program = isset($_GET['program']) ? htmlspecialchars(trim($_GET['program'])) : '';
$filter_status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';

$students = [];
$program_options = [];

$program_sql = "SELECT DISTINCT program_name FROM users WHERE role = 'student' AND program_name IS NOT NULL AND program_name != ''";
$program_result = mysqli_query($conn, $program_sql);
if ($program_result) {
    while ($row = mysqli_fetch_assoc($program_result)) {
        $program_options[] = htmlspecialchars($row['program_name']);
    }
} else {
    error_log("Error fetching program options: " . mysqli_error($conn));
}

$sql = "SELECT utmid, username, email, program_name, registration_status, phone_number FROM users WHERE role = 'student'";
$params = [];
$types = '';

if (!empty($search_query)) {
    $sql .= " AND (username LIKE ? OR utmid LIKE ? OR email LIKE ?)";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
    $types .= 'sss';
}

if (!empty($filter_program)) {
    $sql .= " AND program_name = ?";
    $params[] = $filter_program;
    $types .= 's';
}

if (!empty($filter_status)) {
    $sql .= " AND registration_status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Failed to prepare student records statement for coordinator: " . mysqli_error($conn));
    $error_message = "Error fetching student records. Please try again.";
} else {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $students[] = array_map('htmlspecialchars', $row);
            }
        } else {
            error_log("Error getting student records result for coordinator: " . mysqli_stmt_error($stmt));
            $error_message = "Error fetching student records. Please try again.";
        }
    } else {
        error_log("Error executing student records statement for coordinator: " . mysqli_stmt_error($stmt));
