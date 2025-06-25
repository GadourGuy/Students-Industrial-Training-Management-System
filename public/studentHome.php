<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is a student
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "A") {
    header("Location: loginPage.html");
    exit;
}

// Fetch student data from database
$utmid = $_SESSION["utmid"];
$sql = "SELECT * FROM student WHERE utmid = '$utmid'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching student data.";
    exit;
}

// Get additional student details (you can expand this based on your database schema)
$student_name = $student['first_name'] . ' ' . $student['last_name'];
$student_email = $student['email'];
$student_utmid = $student['utmid'];

// You can add more fields as needed based on your database structure
// For demonstration, I'll use some placeholder data that you can replace with actual database fields
$program = "Bachelor of Software Engineering"; // Replace with actual field from database
$faculty = "Faculty of Computing"; // Replace with actual field from database
$semester = "202520261"; // Replace with actual field from database
$year_course = "SECJH/1"; // Replace with actual field from database
$status = "Approved"; // Replace with actual field from database

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Industrial Training - Student Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" sizes="32x32" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>
  <header class="header">
    <img src="../images/LOGO UTM.png" alt="UTM Logo" class="logo" />
    <nav class="nav-menu">
      <a href="https://www.utm.my/about/">About UTM</a>
      <a href="https://admission.utm.my/undergraduate-malaysian/">Programs</a>
      <a href="./studentEnrollmentCenter.html">Enrollment</a>
      <a href="../backend/logout.php" style="color: #ff4444;">Logout</a>
    </nav>
  </header>

  <main class="container">
    <h1 style="font-size: 48px; font-weight: 700; text-align: center; margin-bottom: 2rem;">Student Center</h1>
    
    <div style="text-align: center; margin-bottom: 2rem;">
      <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
    </div>

    <section class="student-card">
      <div class="student-info">
        <div><?php echo htmlspecialchars($student_name); ?></div>
        <div><?php echo htmlspecialchars($program); ?></div>
        <div><?php echo htmlspecialchars($faculty); ?></div>
        <div><strong>Email:</strong> <?php echo htmlspecialchars($student_email); ?></div>
      </div>
      <div class="student-details">
        <div><strong>ID:</strong> <?php echo htmlspecialchars($student_utmid); ?></div>
        <div><strong>Current Semester:</strong> <?php echo htmlspecialchars($semester); ?></div>
        <div><strong>Year/Course:</strong> <?php echo htmlspecialchars($year_course); ?></div>
        <div><strong>Status:</strong> <span style="color: <?php echo $status == 'Approved' ? 'green' : ($status == 'Pending' ? 'orange' : 'red'); ?>"><?php echo htmlspecialchars($status); ?></span></div>
      </div>
    </section>

    <section class="quick-actions">
      <h2>Quick Actions</h2>
      <div class="action-buttons">
        <div class="action-card" onclick="location.href='student_record.php'">
          <img class="icon" src="../images/Avatar.png" alt="Record Icon" />
          My Record
        </div>
        <div class="action-card" onclick="location.href='programme_registration.php'">
          <img class="icon" src="../images/docIcon.png" alt="Register Icon" />
          Programme Registration
        </div>
        <div class="action-card" onclick="location.href='drop_programme.php'">
          <img class="icon" src="../images/Vector.png" alt="Drop Icon" />
          Drop Programme
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-left">
        <p>&copy; 2025 Universiti Teknologi Malaysia</p>
      </div>
      <div class="footer-links">
        <h4>Quick Links</h4>
        <a href="https://www.utm.my/about/">About UTM</a>
        <a href="./studentEnrollmentCenter.html">Enrollment</a>
        <a href="https://admission.utm.my/undergraduate-malaysian/">Programs</a>
        <a href="https://admission.utm.my/contact-us/">Help/Support</a>
      </div>
      <div class="footer-contact">
        <h4>Contact Information</h4>
        <p><strong>Location:</strong> 81310 UTM Johor Bahru, Johor, Malaysia</p>
        <p><strong>Email:</strong> corporate@utm.my</p>
        <p><strong>Phone:</strong> +6 07-553 3333</p>
      </div>
    </div>
  </footer>
</body>
</html> 