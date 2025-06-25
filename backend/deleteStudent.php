<?php
require('config.php'); // Connect to DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['utmid']) || empty($_POST['utmid'])) {
        http_response_code(400); // Bad Request
        echo "Student ID is required.";
        exit;
    }

    $utmid = mysqli_real_escape_string($conn, $_POST['utmid']);

    $delete_sql = "DELETE FROM student WHERE utmid = '$utmid'";

    if (mysqli_query($conn, $delete_sql)) {
        echo "Success";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error deleting student: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
}
