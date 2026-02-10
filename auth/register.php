<?php
include '../config/db.php';
include '../header.php';

$name = $email = $password = $role = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'] ?? 'student';

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hash, $role);

        if ($stmt->execute()) {
            $message = "User registered successfully.";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Register User</h3>
    <?php if($message): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="student">Student</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
      <a href="../index.php" class="btn btn-link">Back</a>
    </form>
  </div>
</div>

<?php echo "</div></body></html>"; ?>
