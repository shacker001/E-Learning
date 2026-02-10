<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$is_admin = $_SESSION['user']['role'] === 'admin';
$user_id = $_SESSION['user']['id'];
$message = '';

/* Admin: Add new performance record */
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course = trim($_POST['course']);
    $score = $_POST['score'];
    $remarks = trim($_POST['remarks']);

    if ($student_id && $course && $score !== '') {
        $stmt = $conn->prepare("INSERT INTO performance (user_id, course, score, remarks) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $student_id, $course, $score, $remarks);
        $stmt->execute();
        $message = "Performance record added.";
    } else {
        $message = "All fields except remarks are required.";
    }
}

/* Admin: Get all students */
$students = $conn->query("SELECT id, name FROM users WHERE role='student' ORDER BY name");

/* Display performance */
if ($is_admin) {
    $records = $conn->query("
        SELECT performance.*, users.name 
        FROM performance 
        JOIN users ON performance.user_id = users.id
        ORDER BY performance.created_at DESC
    ");
} else {
    $records = $conn->query("
        SELECT * FROM performance 
        WHERE user_id = $user_id
        ORDER BY created_at DESC
    ");
}
?>

<h3>Performance Tracking</h3>

<?php if ($message): ?>
<div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($is_admin): ?>
<div class="card p-4 mb-4">
    <h5>Add Performance Record</h5>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select" required>
                <option value="">Select student</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Course / Module</label>
            <input type="text" name="course" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Score (%)</label>
            <input type="number" name="score" class="form-control" min="0" max="100" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Remarks (optional)</label>
            <textarea name="remarks" class="form-control"></textarea>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>
<?php endif; ?>

<h5><?php echo $is_admin ? "All Students' Performance" : "Your Performance"; ?></h5>

<div class="row g-4">
<?php while ($row = $records->fetch_assoc()): ?>
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <?php if ($is_admin): ?>
                    <h6><i class="bi bi-person"></i> <?php echo $row['name']; ?></h6>
                <?php endif; ?>

                <p><strong>Course:</strong> <?php echo $row['course']; ?></p>
                <p><strong>Score:</strong> <?php echo $row['score']; ?>%</p>

                <?php if ($row['remarks']): ?>
                    <p><strong>Remarks:</strong> <?php echo $row['remarks']; ?></p>
                <?php endif; ?>

                <small class="text-muted">
                    <i class="bi bi-clock"></i> <?php echo $row['created_at']; ?>
                </small>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

</div></body></html>
