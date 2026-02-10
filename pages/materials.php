<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$is_admin = $_SESSION['user']['role'] === 'admin';
$message = '';

if ($is_admin && isset($_FILES['file'])) {
    $title = trim($_POST['title']);
    $file  = $_FILES['file']['name'];

    if ($title && $file) {
        $target = "../uploads/" . basename($file);
        move_uploaded_file($_FILES['file']['tmp_name'], $target);

        $stmt = $conn->prepare("INSERT INTO materials (title, filename) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $file);
        $stmt->execute();

        $message = "Material uploaded.";
    }
}

$materials = $conn->query("SELECT * FROM materials ORDER BY uploaded_at DESC");
?>

<h3>Learning Materials</h3>

<?php if ($message): ?>
<div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<?php if ($is_admin): ?>
<div class="card mb-4">
  <div class="card-body">
    <h5>Upload Material</h5>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">File</label>
        <input type="file" name="file" class="form-control" required>
      </div>

      <button class="btn btn-primary">Upload</button>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="row g-4">
<?php while ($row = $materials->fetch_assoc()): ?>
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title">
          <i class="bi bi-file-earmark-text text-success"></i>
          <?php echo $row['title']; ?>
        </h5>
        <p class="card-text">
          <i class="bi bi-clock"></i>
          <?php echo $row['uploaded_at']; ?>
        </p>
        <a href="../uploads/<?php echo $row['filename']; ?>" download class="btn btn-success btn-sm">
          <i class="bi bi-download"></i> Download
        </a>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>


<?php echo "</div></body></html>"; ?>
