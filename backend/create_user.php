<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $utmid = mysqli_real_escape_string($conn, $_POST['UTMID']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

    if ($password !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    $prefix = strtoupper(substr($utmid, 0, 1));

    switch ($prefix) {
        case 'A':
            $table = 'student';
            $redirect = '../public/studentHome.html';
            break;
        case 'C':
            $table = 'coordinator';
            $redirect = '../public/coordinatorHome.html';
            break;
        case 'M':
            $table = 'admin';
            $redirect = '../public/adminHome.html';
            break;
        default:
            echo "Invalid UTMID prefix.";
            exit;
    }

    // Insert into the correct table
    $sql = "INSERT INTO $table (first_name, last_name, utmid, email, password)
            VALUES ('$firstName', '$lastName', '$utmid', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        header("Location: $redirect");
        exit;
    } else {
        if (mysqli_errno($conn) == 1062) {
            echo "This UTMID is already registered. <a href='../frontend/createAccount.html'>Try again</a>.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>