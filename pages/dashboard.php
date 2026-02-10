<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<h3 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h3>

<div class="row g-4">

  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <i class="bi bi-camera-video fs-1 text-primary"></i>
        <h5 class="mt-3">Training Sessions</h5>
        <p>Manage or join your online sessions.</p>
        <a href="sessions.php" class="btn btn-outline-primary">Open</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <i class="bi bi-cloud-arrow-up fs-1 text-success"></i>
        <h5 class="mt-3">Learning Materials</h5>
        <p>Upload or download study resources.</p>
        <a href="materials.php" class="btn btn-outline-success">Open</a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <i class="bi bi-clipboard-check fs-1 text-warning"></i>
        <h5 class="mt-3">Attendance</h5>
        <p>Mark or review attendance records.</p>
        <a href="attendance.php" class="btn btn-outline-warning">Open</a>
      </div>
    </div>
  </div>

</div>


<?php echo "</div></body></html>"; ?>
