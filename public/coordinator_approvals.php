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

$students_sql = "SELECT * FROM enrollment ORDER BY matric_no ASC";
$students_result = mysqli_query($conn, $students_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records - Coordinator Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="stylesheet" href="../style/tableStyle.css" />
  <link rel="stylesheet" href="../style/studentTableStyle.css" />
</head>
<body>
  <header class="header">
    <img src="../images/LOGO UTM.png" alt="UTM Logo" class="logo" />
    <nav class="nav-menu">
      <a href="https://www.utm.my/about/">About UTM</a>
      <a href="https://admission.utm.my/undergraduate-malaysian/">Programs</a>
      <a href="./studentEnrollmentCenter.html">Enrollment</a>
      <a href="../backend/logout.php" style="color: #ff4444;">Logout</a>
    </nav>
  </header>

            $update_user_sql = "UPDATE users SET registration_status = ? WHERE utmid = ?";
            $update_user_stmt = mysqli_prepare($conn, $update_user_sql);

            if ($update_user_stmt === false) {
                throw new Exception("Failed to prepare update user status statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($update_user_stmt, "
