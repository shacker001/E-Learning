<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = "user_" . $user_id . "." . $ext;
        $target = "../uploads/profile_pics/" . $newName;

        move_uploaded_file($file['tmp_name'], $target);

        $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
        $stmt->bind_param("si", $newName, $user_id);
        $stmt->execute();

        $_SESSION['user']['profile_pic'] = $newName;
        $message = "Profile picture updated successfully.";
    } else {
        $message = "Error uploading file.";
    }
}

$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
?>

<h3>Update Profile Picture</h3>

<?php if ($message): ?>
<div class="alert alert-info"><?php echo $message; ?></div>
<?php endif; ?>

<div class="card p-4" style="max-width: 400px;">
    <img src="../uploads/profile_pics/<?php echo $user['profile_pic']; ?>" 
         class="rounded-circle mb-3" width="120" height="120">

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Choose New Picture</label>
            <input type="file" name="profile_pic" class="form-control" required>
        </div>
        <button class="btn btn-primary">Upload</button>
    </form>
</div>

</div></body></html>
