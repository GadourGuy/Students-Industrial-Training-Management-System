<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is a coordinator
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "C") {
    header("Location: loginPage.html");
    exit;
}

// Fetch coordinator data
$coordinator_utmid = $_SESSION["utmid"];
$coordinator_sql = "SELECT first_name, last_name FROM coordinator WHERE utmid = '$coordinator_utmid'";
$coordinator_result = mysqli_query($conn, $coordinator_sql);
$coordinator = mysqli_fetch_assoc($coordinator_result);
$coordinator_name = $coordinator['first_name'] . ' ' . $coordinator['last_name'];

// Fetch all students from database
$students_sql = "SELECT * FROM student ORDER BY utmid ASC";
$students_result = mysqli_query($conn, $students_sql);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Records - Coordinator Dashboard</title>
  <link rel="icon" href="https://brand.utm.my/wp-content/uploads/sites/21/2020/08/cropped-UTMsiteicon-32x32.png" sizes="32x32" />
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    .records-table {
      width: 100%;
      border-collapse: collapse;
      margin: 2rem 0;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .records-table th,
    .records-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    .records-table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.9rem;
      letter-spacing: 0.5px;
    }
    
    .records-table tr:hover {
      background-color: #f5f5f5;
    }
    
    .records-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    
    .back-button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 1rem;
      transition: transform 0.2s;
    }
    
    .back-button:hover {
      transform: translateY(-2px);
      color: white;
    }
    
    .student-id {
      font-weight: 600;
      color: #667eea;
    }
    
    .search-container {
      margin: 1rem 0;
    }
    
    .search-input {
      width: 300px;
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }
    
    .total-count {
      background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      display: inline-block;
      margin: 1rem 0;
      font-weight: 600;
    }
  </style>
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
    <a href="coordinatorHome.php" class="back-button">← Back to Dashboard</a>
    
    <h1 style="font-size: 42px; font-weight: 700; text-align: center; margin-bottom: 1rem;">Student Records</h1>
    <p style="text-align: center; color: #666; margin-bottom: 2rem;">Coordinator: <?php echo htmlspecialchars($coordinator_name); ?></p>
    
    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name or student ID..." onkeyup="searchStudents()">
    </div>
    
    <div class="total-count">
      Total Students: <?php echo mysqli_num_rows($students_result); ?>
    </div>

    <?php if (mysqli_num_rows($students_result) > 0): ?>
      <table class="records-table" id="studentsTable">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
            <tr>
              <td class="student-id"><?php echo htmlspecialchars($student['utmid']); ?></td>
              <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td><?php echo date('M d, Y', strtotime($student['created_at'] ?? 'now')); ?></td>
              <td>
                <button onclick="viewStudent('<?php echo $student['utmid']; ?>')" style="background: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; margin-right: 5px;">View</button>
                <button onclick="editStudent('<?php echo $student['utmid']; ?>')" style="background: #2196F3; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Edit</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
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
        
        if (tdId || tdName) {
          const idValue = tdId.textContent || tdId.innerText;
          const nameValue = tdName.textContent || tdName.innerText;
          
          if (idValue.toUpperCase().indexOf(filter) > -1 || nameValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
          } else {
            tr[i].style.display = 'none';
          }
        }
      }
    }
    
    function viewStudent(studentId) {
      // You can implement student details view here
      alert('Viewing student: ' + studentId);
      // window.location.href = 'student_details.php?id=' + studentId;
    }
    
    function editStudent(studentId) {
      // You can implement student edit functionality here
      alert('Editing student: ' + studentId);
      // window.location.href = 'edit_student.php?id=' + studentId;
    }
  </script>
</body>
</html> 