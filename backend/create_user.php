<?php
session_start();
include('../config.php');

function getPostData($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$utmid = getPostData('utmid');
$username = getPostData('username');
$email = getPostData('email');
$password = getPostData('password');
$confirm_password = getPostData('confirm_password');

if (empty($utmid) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['registration_error'] = "All fields are required.";
    header("Location: ../createAccountPage.html");
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['registration_error'] = "Passwords do not match.";
    header("Location: ../createAccountPage.html");
    exit();
}

$role = '';
$utmid_prefix = substr($utmid, 0, 1);
if ($utmid_prefix == 'A') {
    $role = 'student';
} elseif ($utmid_prefix == 'C') {
    $role = 'coordinator';
} elseif ($utmid_prefix == 'M') {
    $role = 'admin';
} else {
    $_SESSION['registration_error'] = "Invalid UTMID prefix. Must start with A, C, or M.";
    header("Location: ../createAccountPage.html");
    exit();
}

if (!preg_match('/^[ACM]\d{8}$/', $utmid)) {
    $_SESSION['registration_error'] = "Invalid UTMID format. Must be [A/C/M] followed by 8 digits.";
    header("Location: ../createAccountPage.html");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$check_sql = "SELECT utmid FROM users WHERE utmid = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
if ($check_stmt === false) {
    error_log("Failed to prepare check statement: " . mysqli_error($conn));
    $_SESSION['registration_error'] = "An internal server error occurred.";
    header("Location: ../createAccountPage.html");
    exit();
}
mysqli_stmt_bind_param($check_stmt, "s", $utmid);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    $_SESSION['registration_error'] = "UTMID already registered.";
    header("Location: ../createAccountPage.html");
    exit();
}
mysqli_stmt_close($check_stmt);


$sql = "INSERT INTO users (utmid, username, email, password, role) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Failed to prepare insert statement: " . mysqli_error($conn));
    $_SESSION['registration_error'] = "An internal server error occurred. Please try again later.";
    header("Location: ../createAccountPage.html");
    exit();
}

mysqli_stmt_bind_param($stmt, "sssss", $utmid, $username, $email, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['registration_success'] = "Account created successfully! You can now login.";
    header("Location: ../index.html");
    exit();
} else {
    error_log("User registration failed: " . mysqli_stmt_error($stmt));
    $_SESSION['registration_error'] = "Error creating account. Please try again.";
    header("Location: ../createAccountPage.html");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
