<?php
require('config.php'); // Connect to DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $utmid = mysqli_real_escape_string($conn, $_POST['utmid']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE student SET 
                first_name = '$first_name',
                last_name = '$last_name',
                email = '$email'
            WHERE utmid = '$utmid'";

    if (mysqli_query($conn, $sql)) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error updating student: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    http_response_code(405); 
    echo "Invalid request method.";
}
