<?php
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>

<h3>Live Camera Preview</h3>
<p>This shows how your browser captures video and audio (first step toward full WebRTC).</p>

<video id="localVideo" autoplay playsinline class="border" style="max-width: 100%;"></video>

<script>
    async function startPreview() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            const video = document.getElementById('localVideo');
            video.srcObject = stream;
        } catch (err) {
            alert('Could not access camera/microphone: ' + err.message);
        }
    }
    startPreview();
</script>

</div></body></html>
