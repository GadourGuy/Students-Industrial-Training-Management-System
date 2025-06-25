<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $matricNo = mysqli_real_escape_string($conn, $_POST['matricNo']);
    $program = mysqli_real_escape_string($conn, $_POST['program']);
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    $sql = "INSERT INTO enrollment (full_name, matric_no, program, faculty, phone, email, reason)
            VALUES ('$fullName', '$matricNo', '$program', '$faculty', '$phone', '$email', '$reason')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../public/studentHome.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
