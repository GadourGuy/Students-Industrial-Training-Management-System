<?php
session_start();
require("config.php");

if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "A") {
    echo "Unauthorized access.";
    exit;
}

$matric_no = $_SESSION["utmid"];

$check_query = "SELECT * FROM enrollment WHERE matric_no = '$matric_no'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    echo "No enrollment record found to drop.";
    exit;
}

$delete_query = "DELETE FROM enrollment WHERE matric_no = '$matric_no'";
if (mysqli_query($conn, $delete_query)) {
    echo "success";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
