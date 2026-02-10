<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$is_admin = $_SESSION['user']['role'] === 'admin';

if (isset($_GET['mark'])) {
    $session_id = intval($_GET['mark']);

    $stmt = $conn->prepare("INSERT INTO attendance (user_id, session_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $session_id);
    $stmt->execute();
}

$sessions = $conn->query("SELECT * FROM sessions ORDER BY session_date DESC");

$attendance = $conn->query("
    SELECT attendance.*, sessions.title 
    FROM attendance 
    JOIN sessions ON attendance.session_id = sessions.id
    WHERE attendance.user_id = $user_id
");
?>

<h3>Attendance</h3>

<h5>Mark Attendance</h5>
<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>Session</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $sessions->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['title']; ?></td>
      <td><?php echo $row['session_date']; ?></td>
      <td>
        <a href="?mark=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Mark Attendance</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<h5>Your Attendance Records</h5>
<ul class="list-group shadow-sm">
<?php while ($row = $attendance->fetch_assoc()): ?>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span>
      <i class="bi bi-check-circle text-success"></i>
      <?php echo $row['title']; ?>
    </span>
    <span class="text-muted"><?php echo $row['attended_at']; ?></span>
  </li>
<?php endwhile; ?>
</ul>


<?php echo "</div></body></html>"; ?>
