<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $utmid = $_POST['utmid'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($utmid && $first_name && $last_name && $email) {
        $stmt = $conn->prepare("UPDATE coordinator SET first_name = ?, last_name = ?, email = ? WHERE utmid = ?");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $utmid);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error updating coordinator: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Missing fields.";
    }

    $conn->close();
}
?>
