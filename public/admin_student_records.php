<?php
session_start();
require('../backend/config.php');

if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "M") {
    header("Location: loginPage.html");
    exit;
}

$admin_utmid = $_SESSION["utmid"];
$admin_sql = "SELECT first_name, last_name FROM admin WHERE utmid = '$admin_utmid'";
$admin_result = mysqli_query($conn, $admin_sql);
$admin = mysqli_fetch_assoc($admin_result);
$admin_name = $admin['first_name'] . ' ' . $admin['last_name'];

$students_sql = "SELECT * FROM student ORDER BY utmid ASC";
$students_result = mysqli_query($conn, $students_sql);

$total_students = mysqli_num_rows($students_result);
$coordinator_count_sql = "SELECT COUNT(*) as count FROM coordinator";
$coordinator_count_result = mysqli_query($conn, $coordinator_count_sql);
$total_coordinators = mysqli_fetch_assoc($coordinator_count_result)['count'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records - Admin Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" sizes="32x32" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="stylesheet" href="../style/adminTableStyle.css" />
  <link rel="stylesheet" href="../style/adminModalStyle.css" />
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
    <a href="adminHome.php" class="simple-back-link">&larr; Back to Admin Dashboard</a>

    <h1 class="admin-page-title">Student Records Management</h1>
    <p class="admin-subtitle">Administrator: <?php echo htmlspecialchars($admin_name); ?></p>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-number"><?php echo $total_students; ?></div>
        <div class="stat-label">Total Students</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo $total_coordinators; ?></div>
        <div class="stat-label">Total Coordinators</div>
      </div>
      <div class="stat-card">
        <div class="stat-number"><?php echo date('Y'); ?></div>
        <div class="stat-label">Current Year</div>
      </div>
    </div>

    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name, ID, or email..." onkeyup="searchStudents()">
      <select id="filterSelect" class="filter-select" onchange="filterStudents()">
        <option value="">All Students</option>
        <option value="A">Students (A prefix)</option>
      </select>
    </div>

    <?php if ($total_students > 0): ?>
      <table class="records-table" id="studentsTable">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Admin Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php mysqli_data_seek($students_result, 0); while ($student = mysqli_fetch_assoc($students_result)): ?>
            <tr>
              <td><input type="checkbox" class="student-checkbox" value="<?php echo $student['utmid']; ?>"></td>
              <td class="student-id"><?php echo htmlspecialchars($student['utmid']); ?></td>
              <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td><?php echo date('M d, Y', strtotime($student['created_at'] ?? 'now')); ?></td>
              <td>
                <button class="action-btn edit" onclick="openEditModal('<?php echo $student['utmid']; ?>')">Edit</button>
                <button class="action-btn danger" onclick="deleteStudent('<?php echo $student['utmid']; ?>')">Delete</button>
              </td>
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

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Edit Student</h2>
      <form id="editForm">
        <input type="hidden" id="edit_utmid" name="utmid">
        <label>First Name:</label>
        <input type="text" id="edit_first_name" name="first_name" required>
        <label>Last Name:</label>
        <input type="text" id="edit_last_name" name="last_name" required>
        <label>Email:</label>
        <input type="email" id="edit_email" name="email" required>
        <button type="submit" class="action-btn edit">Save Changes</button>
      </form>
    </div>
  </div>

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

  <script src="../scripts/adminTableLogic.js"></script>
</body>
</html>
