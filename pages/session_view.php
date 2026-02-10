<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$session = $conn->query("SELECT * FROM sessions WHERE id=$id")->fetch_assoc();

if (!$session) {
    echo "<p>Session not found.</p></div></body></html>";
    exit;
}
?>

<h3><?php echo htmlspecialchars($session['title']); ?></h3>
<p><i class="bi bi-calendar-event"></i> <?php echo $session['session_date']; ?></p>

<div class="card p-3 mb-3">
    <h5>Live Class</h5>
    <p>Use the embedded window below or open in a new tab.</p>

    <div class="ratio ratio-16x9 mb-3">
        <iframe 
            src="<?php echo htmlspecialchars($session['session_link']); ?>" 
            allow="camera; microphone; fullscreen; display-capture"
            style="border:0;">
        </iframe>
    </div>

    <a href="<?php echo htmlspecialchars($session['session_link']); ?>" 
       target="_blank" class="btn btn-primary">
       Open in New Tab
    </a>
</div>

</div></body></html>
