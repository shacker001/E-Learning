<?php
include '../header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<h3>Screen Sharing Preview</h3>
<p>This shows exactly what your browser will share with trainees.</p>

<video id="screenVideo" autoplay playsinline class="border" style="width:100%; max-height:70vh;"></video>

<button id="startShare" class="btn btn-primary mt-3">
    Start Screen Sharing
</button>

<script>
document.getElementById('startShare').addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getDisplayMedia({
            video: true,
            audio: true
        });

        const video = document.getElementById('screenVideo');
        video.srcObject = stream;

        // When user stops sharing
        stream.getVideoTracks()[0].addEventListener('ended', () => {
            alert("Screen sharing stopped");
        });

    } catch (err) {
        alert("Could not start screen sharing: " + err.message);
    }
});
</script>

</div></body></html>
