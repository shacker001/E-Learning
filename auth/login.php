<?php
include '../config/db.php';
include '../header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'name'=> $user['name'],
            'role'=> $user['role'],
            'email'=>$user['email']
        ];
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        $message = "Invalid email or password.";
    }
    $_SESSION['user']['profile_pic'] = $user['profile_pic'];

}
?>

<div class="row justify-content-center">
  <div class="col-md-4">
    <h3>Login</h3>
    <?php if($message): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
      <a href="../index.php" class="btn btn-link">Back</a>
    </form>
  </div>
</div>

<?php echo "</div></body></html>"; ?>
