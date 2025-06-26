<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is a coordinator
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "C") {
    header("Location: loginPage.html");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matric_no']) && isset($_POST['status'])) {
    $matric_no = $_POST['matric_no'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE enrollment SET approval_status = ? WHERE matric_no = ?");
    $stmt->bind_param("ss", $status, $matric_no);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
    exit;
}

// Normal page flow
$coordinator_utmid = $_SESSION["utmid"];
$coordinator_sql = "SELECT first_name, last_name FROM coordinator WHERE utmid = '$coordinator_utmid'";
$coordinator_result = mysqli_query($conn, $coordinator_sql);
$coordinator = mysqli_fetch_assoc($coordinator_result);
$coordinator_name = $coordinator['first_name'] . ' ' . $coordinator['last_name'];

$students_sql = "SELECT * FROM enrollment ORDER BY matric_no ASC";
$students_result = mysqli_query($conn, $students_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records - Coordinator Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="stylesheet" href="../style/tableStyle.css" />
  <link rel="stylesheet" href="../style/studentTableStyle.css" />
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

  <main class="student-table-wrapper">
    <h1 class="student-title">Students Applications</h1>

    <div class="student-toolbar">
      <input type="text" id="searchInput" placeholder="Search by name or student ID..." onkeyup="searchStudents()" class="student-search" />
      <div class="student-count">Total Students: <?php echo mysqli_num_rows($students_result); ?></div>
    </div>

    <table id="studentsTable" class="students-table">
      <thead>
        <tr>
          <th>Matric No</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Registration Date</th>
          <th>Message</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
          <tr>
            <td class="student-id"><?php echo htmlspecialchars($student['matric_no']); ?></td>
            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
            <td><?php echo htmlspecialchars($student['email']); ?></td>
            <td><?php echo date('M d, Y', strtotime($student['submitted_at'] ?? 'now')); ?></td>
            <td>
              <a href="#" onclick="toggleMessage(this); return false;">Show Message</a>
              <div class="reason-text" style="display: none;"><?php echo nl2br(htmlspecialchars($student['reason'])); ?></div>
            </td>
            <td>
              <div style="display: flex; gap: 8px;">
                <button onclick="updateStatus('<?php echo $student['matric_no']; ?>', 'Approved')" class="btn-approve">Approve</button>
                <button onclick="updateStatus('<?php echo $student['matric_no']; ?>', 'Rejected')" class="btn-reject">Reject</button>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <?php if (mysqli_num_rows($students_result) == 0): ?>
      <div class="no-students-box">
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
      const input = document.getElementById('searchInput').value.toUpperCase();
      const rows = document.querySelectorAll('#studentsTable tbody tr');
      rows.forEach(row => {
        const id = row.cells[0].textContent.toUpperCase();
        const name = row.cells[1].textContent.toUpperCase();
        row.style.display = id.includes(input) || name.includes(input) ? '' : 'none';
      });
    }

    function toggleMessage(link) {
      const div = link.nextElementSibling;
      div.style.display = div.style.display === 'none' ? 'block' : 'none';
      link.textContent = div.style.display === 'block' ? 'Hide Message' : 'Show Message';
    }

    function updateStatus(matricNo, status) {
      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ matric_no: matricNo, status: status })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(`Student ${matricNo} ${status.toLowerCase()} successfully.`);
        } else {
          alert("Failed to update: " + data.error);
        }
      })
      .catch(err => alert("Request failed: " + err));
    }
  </script>
</body>
</html>
