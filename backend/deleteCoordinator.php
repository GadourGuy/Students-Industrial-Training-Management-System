<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['utmid'])) {
    $utmid = $_POST['utmid'];

    $stmt = $conn->prepare("DELETE FROM coordinator WHERE utmid = ?");
    $stmt->bind_param("s", $utmid);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting coordinator: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
