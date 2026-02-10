<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$is_admin = $_SESSION['user']['role'] === 'admin';
$message = '';

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $date  = $_POST['session_date'];
    $link  = trim($_POST['session_link']);

    if ($title && $date && $link) {
        $stmt = $conn->prepare("INSERT INTO sessions (title, session_date, session_link) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $date, $link);
        $stmt->execute();
        $message = "Session added successfully.";
    } else {
        $message = "All fields are required.";
    }
}

if ($is_admin && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM sessions WHERE id = $id");
    $message = "Session deleted.";
}

$sessions = $conn->query("SELECT * FROM sessions ORDER BY session_date DESC");
?>

<h3>Training Sessions</h3>

<?php if ($message): ?>
    <div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($is_admin): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5>Add New Session</h5>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Session Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date & Time</label>
                    <input type="datetime-local" name="session_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Session Link</label>
                    <input type="text" name="session_link" class="form-control" required>
                </div>

                <button class="btn btn-primary">Add Session</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php while ($row = $sessions->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-camera-video text-primary"></i>
                        <?php echo htmlspecialchars($row['title']); ?>
                    </h5>
                    <p class="card-text">
                        <i class="bi bi-calendar-event"></i>
                        <?php echo $row['session_date']; ?>
                    </p>
                    <a href="session_view.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                        Open Class
                    </a>

                    <?php if ($is_admin): ?>
                        <a href="?delete=<?php echo $row['id']; ?>"
                            class="btn btn-danger btn-sm float-end"
                            onclick="return confirm('Delete this session')">
                            <i class="bi bi-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>


<?php echo "</div></body></html>"; ?>