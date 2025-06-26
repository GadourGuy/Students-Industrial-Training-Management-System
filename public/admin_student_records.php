<?php
session_start();
include('../config.php');

if (!isset($_SESSION['utmid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$search_query = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$filter_program = isset($_GET['program']) ? htmlspecialchars(trim($_GET['program'])) : '';
$filter_status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';

$students = [];
$program_options = [];

$program_sql = "SELECT DISTINCT program_name FROM users WHERE role = 'student' AND program_name IS NOT NULL AND program_name != ''";
$program_result = mysqli_query($conn, $program_sql);
if ($program_result) {
    while ($row = mysqli_fetch_assoc($program_result)) {
        $program_options[] = htmlspecialchars($row['program_name']);
    }
} else {
    error_log("Error fetching program options: " . mysqli_error($conn));
}

$sql = "SELECT utmid, username, email, program_name, registration_status, phone_number FROM users WHERE role = 'student'";
$params = [];
$types = '';

if (!empty($search_query)) {
    $sql .= " AND (username LIKE ? OR utmid LIKE ? OR email LIKE ?)";
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
    $params[] = '%' . $search_query . '%';
    $types .= 'sss';
}

if (!empty($filter_program)) {
    $sql .= " AND program_name = ?";
    $params[] = $filter_program;
    $types .= 's';
}

if (!empty($filter_status)) {
    $sql .= " AND registration_status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Failed to prepare student records statement: " . mysqli_error($conn));
    $error_message = "Error fetching student records. Please try again.";
} else {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $students[] = array_map('htmlspecialchars', $row);
            }
        } else {
            error_log("Error getting student records result: " . mysqli_stmt_error($stmt));
            $error_message = "Error fetching student records. Please try again.";
        }
    } else {
        error_log("Error executing student records statement: " . mysqli_stmt_error($stmt));
        $error_message = "Error fetching student records. Please try again.";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Student Records</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <header class="header">
        <img src="../images/LOGO UTM.png" alt="UTM Logo" class="logo">
        <nav class="nav-menu">
            <a href="../adminHome.php">Dashboard</a>
            <a href="admin_students_records.php">Student Records</a>
            <a href="admin_coordinator_records.php">Coordinator Records</a>
            <a href="admin_registrations.php">Applications</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <h2>Manage Student Records</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="admin_students_records.php" method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search by Name, UTMID, Email" value="<?php echo htmlspecialchars($search_query); ?>">

            <select name="program">
                <option value="">All Programs</option>
                <?php foreach ($program_options as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ($filter_program === $option) ? 'selected' : ''; ?>>
                        <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">All Statuses</option>
                <option value="pending" <?php echo ($filter_status === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo ($filter_status === 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo ($filter_status === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>

            <button type="submit" class="button">Apply Filters</button>
            <a href="admin_students_records.php" class="button" style="background-color: #6c757d;">Clear Filters</a>
        </form>

        <?php if (empty($students)): ?>
            <p>No student records found matching your criteria.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>UTMID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo $student['utmid']; ?></td>
                                <td><?php echo $student['username']; ?></td>
                                <td><?php echo $student['email']; ?></td>
                                <td><?php echo $student['program_name']; ?></td>
                                <td><?php echo $student['registration_status']; ?></td>
                                <td><?php echo $student['phone_number']; ?></td>
                                <td>
                                    <a href="editStudent.php?utmid=<?php echo $student['utmid']; ?>" class="button" style="background-color: #007bff;">Edit</a>
                                    <a href="deleteStudent.php?utmid=<?php echo $student['utmid']; ?>" class="button" style="background-color: #dc3545;" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
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
