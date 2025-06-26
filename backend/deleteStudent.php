<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

if (isset($_GET['utmid'])) {
    $utmid_to_delete = htmlspecialchars(trim($_GET['utmid']));

    $sql = "DELETE FROM users WHERE utmid = ? AND role = 'student'";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        error_log("Failed to prepare delete student statement: " . mysqli_error($conn));
        $_SESSION['message'] = "An internal server error occurred while preparing to delete student.";
        $_SESSION['message_type'] = 'error';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $utmid_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['message'] = "Student with UTMID " . $utmid_to_delete . " deleted successfully!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Student with UTMID " . $utmid_to_delete . " not found or could not be deleted.";
                $_SESSION['message_type'] = 'error';
            }
        } else {
            error_log("Error executing delete student statement: " . mysqli_stmt_error($stmt));
            $_SESSION['message'] = "Error deleting student record.";
            $_SESSION['message_type'] = 'error';
        }
        mysqli_stmt_close($stmt);
    }
} else {
    $_SESSION['message'] = "No student UTMID provided for deletion.";
    $_SESSION['message_type'] = 'error';
}

mysqli_close($conn);
header("Location: admin_students_records.php");
exit();
?>
