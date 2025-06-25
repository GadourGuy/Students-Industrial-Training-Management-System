<?php
session_start();
require('../backend/config.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION["Login"]) || $_SESSION["Login"] != "YES" || $_SESSION["ROLE"] != "M") {
    header("Location: loginPage.html");
    exit;
}

// Fetch admin data
$admin_utmid = $_SESSION["utmid"];
$admin_sql = "SELECT first_name, last_name FROM admin WHERE utmid = '$admin_utmid'";
$admin_result = mysqli_query($conn, $admin_sql);
$admin = mysqli_fetch_assoc($admin_result);
$admin_name = $admin['first_name'] . ' ' . $admin['last_name'];

// Fetch all students from database
$students_sql = "SELECT * FROM student ORDER BY utmid ASC";
$students_result = mysqli_query($conn, $students_sql);

// Get statistics
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
      background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
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
      background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
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
      color: #FF6B6B;
    }
    
    .search-container {
      margin: 1rem 0;
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    
    .search-input {
      width: 300px;
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }
    
    .filter-select {
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin: 2rem 0;
    }
    
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    
    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
    }
    
    .admin-actions {
      background: white;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin: 1rem 0;
    }
    
    .action-btn {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 5px;
      margin-bottom: 5px;
      transition: background 0.2s;
    }
    
    .action-btn:hover {
      background: #45a049;
    }
    
    .action-btn.danger {
      background: #f44336;
    }
    
    .action-btn.danger:hover {
      background: #da190b;
    }
    
    .action-btn.edit {
      background: #2196F3;
    }
    
    .action-btn.edit:hover {
      background: #0b7dda;
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
    <a href="adminHome.php" class="back-button">← Back to Admin Dashboard</a>
    
    <h1 style="font-size: 42px; font-weight: 700; text-align: center; margin-bottom: 1rem;">Student Records Management</h1>
    <p style="text-align: center; color: #666; margin-bottom: 2rem;">Administrator: <?php echo htmlspecialchars($admin_name); ?></p>
    
    <!-- Statistics Cards -->
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
    
    <!-- Search and Filter -->
    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name, ID, or email..." onkeyup="searchStudents()">
      <select id="filterSelect" class="filter-select" onchange="filterStudents()">
        <option value="">All Students</option>
        <option value="A">Students (A prefix)</option>
        <!-- Add more filters as needed -->
      </select>
    </div>

    <?php if ($total_students > 0): ?>
      <table class="records-table" id="studentsTable">
        <thead>
          <tr>
            <th>
              <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
            </th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Admin Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          mysqli_data_seek($students_result, 0); // Reset pointer
          while ($student = mysqli_fetch_assoc($students_result)): 
          ?>
            <tr>
              <td>
                <input type="checkbox" class="student-checkbox" value="<?php echo $student['utmid']; ?>">
              </td>
              <td class="student-id"><?php echo htmlspecialchars($student['utmid']); ?></td>
              <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td><?php echo date('M d, Y', strtotime($student['created_at'] ?? 'now')); ?></td>
              <td>
                <button class="action-btn" onclick="viewStudent('<?php echo $student['utmid']; ?>')">👁️ View</button>
                <button class="action-btn edit" onclick="editStudent('<?php echo $student['utmid']; ?>')">✏️ Edit</button>
                <button class="action-btn danger" onclick="deleteStudent('<?php echo $student['utmid']; ?>')">🗑️ Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h3>No Students Found</h3>
        <p>There are currently no students registered in the system.</p>
        <button class="action-btn" onclick="addStudent()">Add First Student</button>
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
        const tdId = tr[i].getElementsByTagName('td')[1];
        const tdName = tr[i].getElementsByTagName('td')[2];
        const tdEmail = tr[i].getElementsByTagName('td')[3];
        
        if (tdId && tdName && tdEmail) {
          const idValue = tdId.textContent || tdId.innerText;
          const nameValue = tdName.textContent || tdName.innerText;
          const emailValue = tdEmail.textContent || tdEmail.innerText;
          
          if (idValue.toUpperCase().indexOf(filter) > -1 || 
              nameValue.toUpperCase().indexOf(filter) > -1 || 
              emailValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
          } else {
            tr[i].style.display = 'none';
          }
        }
      }
    }
    
    function filterStudents() {
      const select = document.getElementById('filterSelect');
      const filter = select.value;
      const table = document.getElementById('studentsTable');
      const tr = table.getElementsByTagName('tr');

      for (let i = 1; i < tr.length; i++) {
        const tdId = tr[i].getElementsByTagName('td')[1];
        
        if (tdId) {
          const idValue = tdId.textContent || tdId.innerText;
          
          if (filter === '' || idValue.startsWith(filter)) {
            tr[i].style.display = '';
          } else {
            tr[i].style.display = 'none';
          }
        }
      }
    }
    
    function toggleSelectAll() {
      const selectAll = document.getElementById('selectAll');
      const checkboxes = document.getElementsByClassName('student-checkbox');
      
      for (let checkbox of checkboxes) {
        checkbox.checked = selectAll.checked;
      }
    }
    
    function viewStudent(studentId) {
      alert('Viewing student details: ' + studentId);
      // window.location.href = 'admin_student_details.php?id=' + studentId;
    }
    
    function editStudent(studentId) {
      alert('Editing student: ' + studentId);
      // window.location.href = 'admin_edit_student.php?id=' + studentId;
    }
    
    function deleteStudent(studentId) {
      if (confirm('Are you sure you want to delete student ' + studentId + '? This action cannot be undone.')) {
        alert('Delete functionality would be implemented here');
        // Implementation: Send AJAX request to delete student
      }
    }
    
    function exportData() {
      alert('Export functionality would be implemented here');
      // Implementation: Generate CSV/Excel export
    }
    
    function addStudent() {
      alert('Add student functionality would be implemented here');
      // window.location.href = 'admin_add_student.php';
    }
    
    function bulkActions() {
      const checked = document.querySelectorAll('.student-checkbox:checked');
      if (checked.length === 0) {
        alert('Please select at least one student');
        return;
      }
      alert('Bulk actions for ' + checked.length + ' students');
    }
    
    function generateReport() {
      alert('Report generation would be implemented here');
      // window.location.href = 'admin_reports.php';
    }
  </script>
</body>
</html> 