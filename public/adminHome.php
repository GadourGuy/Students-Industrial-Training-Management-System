<?php
session_start();
include('config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

$total_students = 0;
$total_coordinators = 0;
$pending_applications = 0;
$approved_students = 0;
$rejected_students = 0;

$admin_username = htmlspecialchars($_SESSION['username']);

$conn_error = false;
if ($conn === false) {
    error_log("Database connection failed in adminHome.php: " . mysqli_connect_error());
    $conn_error = true;
} else {
    $sqls = [
        'total_students' => "SELECT COUNT(*) AS count FROM users WHERE role = 'student'",
        'total_coordinators' => "SELECT COUNT(*) AS count FROM users WHERE role = 'coordinator'",
        'pending_applications' => "SELECT COUNT(*) AS count FROM users WHERE role = 'student' AND registration_status = 'pending'",
        'approved_students' => "SELECT COUNT(*) AS count FROM users WHERE role = 'student' AND registration_status = 'approved'",
        'rejected_students' => "SELECT COUNT(*) AS count FROM users WHERE role = 'student' AND registration_status = 'rejected'"
    ];

    foreach ($sqls as $key => $sql) {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            error_log("Failed to prepare statement for " . $key . ": " . mysqli_error($conn));
            continue;
        }
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                if ($key === 'total_students') {
                    $total_students = htmlspecialchars($row['count']);
                } elseif ($key === 'total_coordinators') {
                    $total_coordinators = htmlspecialchars($row['count']);
                } elseif ($key === 'pending_applications') {
                    $pending_applications = htmlspecialchars($row['count']);
                } elseif ($key === 'approved_students') {
                    $approved_students = htmlspecialchars($row['count']);
                } elseif ($key === 'rejected_students') {
                    $rejected_students = htmlspecialchars($row['count']);
                }
            } else {
                error_log("Error getting result for " . $key . ": " . mysqli_stmt_error($stmt));
            }
        } else {
            error_log("Error executing statement for " . $key . ": " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt
