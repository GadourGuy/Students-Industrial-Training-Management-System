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

$coordinators_sql = "SELECT * FROM coordinator ORDER BY utmid ASC";
$coordinators_result = mysqli_query($conn, $coordinators_sql);
$total_coordinators = mysqli_num_rows($coordinators_result);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Coordinator Records - Admin Dashboard</title>
  <link rel="stylesheet" href="../style/style.css" />
  <link rel="stylesheet" href="../style/homeStyle.css" />
  <link rel="stylesheet" href="../style/adminTableStyle.css" />
  <link rel="stylesheet" href="../style/adminModalStyle.css" />
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

    <h1 class="admin-page-title">Coordinator Records Management</h1>
    <p class="admin-subtitle">Administrator: <?php echo htmlspecialchars($admin_name); ?></p>

    <div class="search-container">
      <input type="text" id="searchInput" class="search-input" placeholder="Search by name, ID, or email..." onkeyup="searchCoordinators()">
    </div>

    <?php if ($total_coordinators > 0): ?>
      <table class="records-table" id="coordinatorsTable">
        <thead>
          <tr>
            <th>Coordinator ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Registration Date</th>
            <th>Admin Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($coordinator = mysqli_fetch_assoc($coordinators_result)): ?>
            <tr>
              <td class="coordinator-id"><?php echo htmlspecialchars($coordinator['utmid']); ?></td>
              <td><?php echo htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']); ?></td>
              <td><?php echo htmlspecialchars($coordinator['email']); ?></td>
              <td><?php echo date('M d, Y', strtotime($coordinator['created_at'] ?? 'now')); ?></td>
              <td>
                <button class="action-btn edit" onclick="openEditModal('<?php echo $coordinator['utmid']; ?>')">Edit</button>
                <button class="action-btn danger" onclick="deleteCoordinator('<?php echo $coordinator['utmid']; ?>')">Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-box">
        <h3>No Coordinators Found</h3>
        <p>There are currently no coordinators registered in the system.</p>
      </div>
    <?php endif; ?>
  </main>

  <!-- Edit Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Edit Coordinator</h2>
    <form id="editForm">
      <input type="hidden" id="edit_utmid" name="utmid">

      <label for="edit_first_name">First Name:</label>
      <input type="text" id="edit_first_name" name="first_name" placeholder="Enter first name" required>

      <label for="edit_last_name">Last Name:</label>
      <input type="text" id="edit_last_name" name="last_name" placeholder="Enter last name" required>

      <label for="edit_email">Email:</label>
      <input type="email" id="edit_email" name="email" placeholder="Enter email address" required>

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

  <script src="../scripts/adminCoordinatorTableLogic.js"></script>
</body>
</html>
