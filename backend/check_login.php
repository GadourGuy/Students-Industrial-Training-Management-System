<?php
session_start();
include('config.php');

function getPostData($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$utmid = getPostData('utmid');
$password = getPostData('password');

if (empty($utmid) || empty($password)) {
    $_SESSION['login_error'] = "UTMID and Password cannot be empty.";
    header("Location: ../index.html");
    exit();
}

$role = '';
if (substr($utmid, 0, 1) == 'A') {
    $role = 'student';
} elseif (substr($utmid, 0, 1) == 'C') {
    $role = 'coordinator';
} elseif (substr($utmid, 0, 1) == 'M') {
    $role = 'admin';
} else {
    $_SESSION['login_error'] = "Invalid UTMID format.";
    header("Location: ../index.html");
    exit();
}

$sql = "SELECT utmid, password FROM users WHERE utmid = ? AND role = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Failed to prepare statement: " . mysqli_error($conn));
    $_SESSION['login_error'] = "An internal server error occurred. Please try again later.";
    header("Location: ../index.html");
    exit();
}

mysqli_stmt_bind_param($stmt, "ss", $utmid, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $stored_hashed_password = $row['password'];

    //hi. this is izzat. i added a verify password feature.
    if (password_verify($password, $stored_hashed_password)) {
        $_SESSION['utmid'] = $utmid;
        $_SESSION['role'] = $role;

        session_regenerate_id(true);

        if ($role == 'admin') {
            header("Location: ../adminHome.php");
        } elseif ($role == 'coordinator') {
            header("Location: ../coordinatorHome.php");
        } elseif ($role == 'student') {
            header("Location: ../studentHome.php");
        }
        exit();
    } else {
        // um. invalid password. lol.
        $_SESSION['login_error'] = "Incorrect Password.";
        header("Location: ../index.html");
        exit();
    }
} else {
    // this is a user not found thingy
    $_SESSION['lisogin_error'] = "User not found or role mmatch.";
    header("Location: ../index.html");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
