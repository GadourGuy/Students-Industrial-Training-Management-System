<?php
session_start();
require('../backend/config.php');

// Ensure user is logged in as coordinator
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "C") {
    header("Location: loginPage.html");
    exit;
}

// Get coordinator details
$coordinator_utmid = $_SESSION["utmid"];
$coordinator_sql = "SELECT first_name, last_name FROM coordinator WHERE utmid = '$coordinator_utmid'";
$coordinator_result = mysqli_query($conn, $coordinator_sql);
$coordinator = mysqli_fetch_assoc($coordinator_result);
$coordinator_name = $coordinator['first_name'] . ' ' . $coordinator['last_name'];

// Fetch students
$students_sql = "SELECT * FROM student ORDER BY utmid ASC";
$students_result = mysqli_query($conn, $students_sql);
$total_students = mysqli_num_rows($students_result);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records - Coordinator View</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" sizes="32x32" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="stylesheet" href="../style/adminTableStyle.css" />
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
    <a href="coordinatorHome.php" class="simple-back-link">&larr; Back to Coordinator Dashboard</a>

    <h1 class="admin-page-title">Student Records</h1>
    <p class="admin-subtitle">Coordinator: <?php echo htmlspecialchars($coordinator_name); ?></p>

    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name, ID, or email..." onkeyup="searchStudents()">
    </div>

    <?php if ($total_students > 0): ?>
      <table class="records-table" id="studentsTable">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Registered On</th>
          </tr>
        </thead>
        <tbody>
          <?php mysqli_data_seek($students_result, 0); while ($student = mysqli_fetch_assoc($students_result)): ?>
            <tr>
              <td class="student-id"><?php echo htmlspecialchars($student['utmid']); ?></td>
              <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td><?php echo date('M d, Y', strtotime($student['created_at'] ?? 'now')); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-box">
        <h3>No Students Found</h3>
        <p>There are currently no students registered in the system.</p>
      </div>
    <?php endif; ?>
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

  <script>
    function searchStudents() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const table = document.getElementById('studentsTable');
      const tr = table.getElementsByTagName('tr');

      for (let i = 1; i < tr.length; i++) {
        const tdId = tr[i].getElementsByTagName('td')[0];
        const tdName = tr[i].getElementsByTagName('td')[1];
        const tdEmail = tr[i].getElementsByTagName('td')[2];
        if (tdId || tdName || tdEmail) {
          const idValue = tdId.textContent || tdId.innerText;
          const nameValue = tdName.textContent || tdName.innerText;
          const emailValue = tdEmail.textContent || tdEmail.innerText;
          if (
            idValue.toUpperCase().indexOf(filter) > -1 ||
            nameValue.toUpperCase().indexOf(filter) > -1 ||
            emailValue.toUpperCase().indexOf(filter) > -1
          ) {
            tr[i].style.display = '';
          } else {
            tr[i].style.display = 'none';
          }
        }
      }
    }
  </script>
</body>
</html>
