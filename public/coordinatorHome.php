<?php
session_start();
include('config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: index.html");
    exit();
}

$total_students_assigned = 0;
$pending_enrollments = 0;
$approved_enrollments = 0;
$rejected_enrollments = 0;

$coordinator_username = htmlspecialchars($_SESSION['username']);

$conn_error = false;
if ($conn === false) {
    error_log("Database connection failed in coordinatorHome.php: " . mysqli_connect_error());
    $conn_error = true;
} else {
    $sqls = [
        'total_students_assigned' => "SELECT COUNT(*) AS count FROM users WHERE role = 'student'",
        'pending_enrollments' => "SELECT COUNT(*) AS count FROM enrollment WHERE status = 'pending'",
        'approved_enrollments' => "SELECT COUNT(*) AS count FROM enrollment WHERE status = 'approved'",
        'rejected_enrollments' => "SELECT COUNT(*) AS count FROM enrollment WHERE status = 'rejected'"
    ];

    foreach ($sqls as $key => $sql) {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            error_log("Failed to prepare statement for " . $key . ": " . mysqli_error($conn));
            continue;
        }
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                if ($key === 'total_students_assigned') {
                    $total_students_assigned = htmlspecialchars($row['count']);
                } elseif ($key === 'pending_enrollments') {
                    $pending_enrollments = htmlspecialchars($row['count']);
                } elseif ($key === 'approved_enrollments') {
                    $approved_enrollments = htmlspecialchars($row['count']);
                } elseif ($key === 'rejected_enrollments') {
                    $rejected_enrollments = htmlspecialchars($row['count']);
                }
            } else {
                error_log("Error getting result for " . $key . ": " . mysqli_stmt_error($stmt));
            }
        } else {
            error_log("Error executing statement for " . $key . ": " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>
    <header class="header">
        <img src="./images/LOGO UTM.png" alt="UTM Logo" class="logo">
        <nav class="nav-menu">
            <a href="coordinatorHome.php">Dashboard</a>
            <a href="./admin/coordinator_student_records.php">Student Records</a>
            <a href="./admin/coordinator_approvals.php">Enrollment Approvals</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <h2>Welcome, <?php echo $coordinator_username; ?> (Coordinator)</h2>

        <?php if ($conn_error): ?>
            <p style="color: red;">Cannot connect to the database. Please check your configuration.</p>
        <?php endif; ?>

        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Students</h3>
                <p><?php echo $total_students_assigned; ?></p>
            </div>
            <div class="card">
                <h3>Pending Enrollments</h3>
                <p><?php echo $pending_enrollments; ?></p>
            </div>
            <div class="card">
                <h3>Approved Enrollments</h3>
                <p><?php echo $approved_enrollments; ?></p>
            </div>
            <div class="card">
                <h3>Rejected Enrollments</h3>
                <p><?php echo $rejected_enrollments; ?></p>
            </div>
        </div>

        <div class="dashboard-sections">
            <section class="section">
                <h3>Quick Actions</h3>
                <ul>
                    <li><a href="./admin/coordinator_approvals.php" class="button">Review Pending Enrollments</a></li>
                    <li><a href="./admin/coordinator_student_records.php" class="button">View Student Records</a></li>
                </ul>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <h4>Quick Links</h4>
                <a href="https://www.utm.my/about/">About UTM</a>
                <a href="https://admission.utm.my/undergraduate-malaysian/">Programs</a>
                <a href="./public/studentEnrollmentCenter.html">Enrollment</a>
            </div>
            <div class="footer-contact">
                <h4>Contact Us</h4>
                <p>Universiti Teknologi Malaysia</p>
                <p>81310 Johor Bahru, Johor, Malaysia</p>
                <p>+607-5533333</p>
            </div>
        </div>
        <p class="footer-bottom">&copy; 2023 Industrial Training Management System. All rights reserved.</p>
    </footer>
</body>
</html>
