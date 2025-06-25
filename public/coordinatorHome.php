<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is a coordinator
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "C") {
    header("Location: loginPage.html");
    exit;
}

// Fetch coordinator data from database
$utmid = $_SESSION["utmid"];
$sql = "SELECT * FROM coordinator WHERE utmid = '$utmid'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $coordinator = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching coordinator data.";
    exit;
}

// Get coordinator details
$coordinator_name = $coordinator['first_name'] . ' ' . $coordinator['last_name'];
$coordinator_email = $coordinator['email'];
$coordinator_utmid = $coordinator['utmid'];

// Get statistics for coordinator dashboard
$student_count_sql = "SELECT COUNT(*) as count FROM student";
$student_count_result = mysqli_query($conn, $student_count_sql);
$student_count = mysqli_fetch_assoc($student_count_result)['count'];




$position = "Industrial Training Coordinator"; 
$department = "Faculty of Computing";

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Industrial Training - Coordinator Dashboard</title>
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
      <a href="../backend/logout.php" style="color: #ff4444;">Logout</a>
    </nav>
  </header>

  <main class="container">
    <h1 style="font-size: 48px; font-weight: 700; text-align: center; margin-bottom: 2rem;">Coordinator Center</h1>
    
    <div style="text-align: center; margin-bottom: 2rem;">
      <h2>Welcome, <?php echo htmlspecialchars($coordinator_name); ?>!</h2>
    </div>

    <section class="student-card">
      <div class="student-info">
        <div><?php echo htmlspecialchars($coordinator_name); ?></div>
        <div><?php echo htmlspecialchars($position); ?></div>
        <div><?php echo htmlspecialchars($department); ?></div>
        <div><strong>Email:</strong> <?php echo htmlspecialchars($coordinator_email); ?></div>
      </div>
      <div class="student-details">
        <div><strong>ID:</strong> <?php echo htmlspecialchars($coordinator_utmid); ?></div>
        <div><strong>Total Students:</strong> <?php echo $student_count; ?></div>
        <div><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></div>
        <div><strong>Role:</strong> <span style="color: #4CAF50;">Coordinator</span></div>
      </div>
    </section>

    <section class="quick-actions">
      <h2>Coordination Actions</h2>
      <div class="action-buttons">
        <div class="action-card" onclick="location.href='coordinator_student_records.php'">
          <img class="icon" src="../images/Avatar.png" alt="Record Icon" />
          Student Records
        </div>
        <div class="action-card" onclick="location.href='coordinator_approvals.php'">
          <img class="icon" src="../images/Vector.png" alt="Approval Icon" />
          Programme Approval
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