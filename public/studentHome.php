<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is a student
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "A") {
    header("Location: loginPage.html");
    exit;
}

// Fetch student data from student table
$utmid = $_SESSION["utmid"];
$sql = "SELECT * FROM student WHERE utmid = '$utmid'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) == 1) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching student data.";
    exit;
}

$student_name = $student['first_name'] . ' ' . $student['last_name'];
$student_email = $student['email'];
$student_utmid = $student['utmid'];

// Fetch enrollment data from enrollment table using matric_no = utmid
$enrollment_sql = "SELECT * FROM enrollment WHERE matric_no = '$utmid' ORDER BY id DESC LIMIT 1";
$enrollment_result = mysqli_query($conn, $enrollment_sql);

if ($enrollment_result && mysqli_num_rows($enrollment_result) == 1) {
    $enrollment = mysqli_fetch_assoc($enrollment_result);
    $program = $enrollment['program'];
    $faculty = $enrollment['faculty'];
    $semester = "202520261";
    $year_course = "SECJH/1";
    $status = $enrollment['approval_status'];
} else {
    $program = "No program registered";
    $faculty = "N/A";
    $semester = "-";
    $year_course = "-";
    $status = "Not Enrolled";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Industrial Training - Student Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
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
        <div><strong>Status:</strong> <span style="color: <?php echo $status == 'Approved' ? 'green' : ($status == 'Pending' ? 'orange' : 'red'); ?>;"><?php echo htmlspecialchars($status); ?></span></div>
      </div>
    </section>

    <section class="quick-actions">
      <h2>Quick Actions</h2>
      <div class="action-buttons">
        <div class="action-card" onclick="showRecordModal()">
          <img class="icon" src="../images/Avatar.png" alt="Record Icon" />
          My Record
        </div>
        <div class="action-card" onclick="location.href='enrollment.html'">
          <img class="icon" src="../images/docIcon.png" alt="Register Icon" />
          Programme Registration
        </div>
        <div class="action-card" onclick="dropProgram()">
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

  <!-- Modal for My Record -->
  <div id="recordModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>My Record</h2>
      <p><strong>Name:</strong> <?php echo $student_name; ?></p>
      <p><strong>UTMID:</strong> <?php echo $student_utmid; ?></p>
      <p><strong>Email:</strong> <?php echo $student_email; ?></p>
      <p><strong>Program:</strong> <?php echo $program; ?></p>
      <p><strong>Faculty:</strong> <?php echo $faculty; ?></p>
      <p><strong>Semester:</strong> <?php echo $semester; ?></p>
      <p><strong>Year/Course:</strong> <?php echo $year_course; ?></p>
      <p><strong>Status:</strong> <?php echo $status; ?></p>
    </div>
  </div>

  <style>
    .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center; z-index: 999; }
    .modal-content { background: white; padding: 30px; border-radius: 10px; width: 80%; max-width: 500px; }
    .close { float: right; font-size: 24px; cursor: pointer; }
  </style>
  <script>
    function showRecordModal() {
      document.getElementById("recordModal").style.display = "flex";
    }

    function closeModal() {
      document.getElementById("recordModal").style.display = "none";
    }

    function dropProgram() {
  if (confirm("Are you sure you want to drop your enrollment?")) {
    fetch("../backend/drop_program.php", {
      method: "POST"
    })
    .then(response => response.text())
    .then(data => {
      if (data.trim() === "success") {
        alert("Enrollment dropped successfully.");
        location.reload();
      } else {
        alert(data); // Show error message
      }
    })
    .catch(err => {
      alert("An error occurred: " + err);
    });
  }
}
  </script>
</body>
</html>
