<?php 
session_start();
require('config.php');

$utmid = $_POST["utmid"];
$password = $_POST["password"];
$prefix = strtoupper(substr($utmid, 0, 1));

if (!isset($_POST["utmid"]) || !isset($_POST["password"])) {
    echo "UTMID or password was not sent. POST data received:<br>";
    var_dump($_POST);
    exit;
}
// Determine table and redirect
switch ($prefix) {
    case 'A':
        $table = 'student';
        $redirect = '../public/studentHome.php';
        break;
    case 'C':
        $table = 'coordinator';
        $redirect = '../public/coordinatorHome.php';
        break;
    case 'M':
        $table = 'admin';
        $redirect = '../public/adminHome.php';
        break;
    default:
        echo "Invalid UTMID prefix.";
        exit;
}

// Query the relevant table
$sql = "SELECT * FROM $table WHERE utmid = '$utmid' AND password = '$password'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    $_SESSION["Login"] = "YES";
    $_SESSION["USER"] = $user["first_name"] . ' ' . $user["last_name"];
    $_SESSION["utmid"] = $user["utmid"];
    $_SESSION["ROLE"] = $prefix;

    header("Location: $redirect");
    exit;
} else {
    $_SESSION["Login"] = "NO";
    header("Location: ../public/loginPage.html?error=invalid");
    exit;
}

mysqli_close($conn);
?>
