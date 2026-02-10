<?php
include '../header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$session_id = intval($_GET['session_id'] ?? 1);
?>

<h3>Share Screen (WebRTC)</h3>
<p>This will broadcast your screen to trainees connected to this session.</p>

<p><strong>Session ID:</strong> <?php echo $session_id; ?></p>

<video id="screenVideo" autoplay playsinline class="border mb-3" style="width:100%; max-height:60vh;"></video>

<button id="startShare" class="btn btn-primary mb-3">
    Start Screen Sharing
</button>

<div id="status" class="text-muted"></div>

<script>
    const sessionId = <?php echo $session_id; ?>;
    let pc;
    let screenStream;

    async function startScreenShare() {
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: true,
                audio: true
            });

            document.getElementById('screenVideo').srcObject = screenStream;

            pc = new RTCPeerConnection({
                iceServers: [{
                    urls: 'stun:stun.l.google.com:19302'
                }]
            });

            screenStream.getTracks().forEach(track => pc.addTrack(track, screenStream));

            const offer = await pc.createOffer();
            await pc.setLocalDescription(offer);

            document.getElementById('status').innerText = 'Sending offer to server...';

            await fetch('../signal.php?action=save_offer&role=trainer&session_id=' + sessionId, {
                method: 'POST',
                body: JSON.stringify(offer),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            document.getElementById('status').innerText = 'Offer sent. Waiting for viewer answer...';

            async function pollForAnswer() {
                const res = await fetch('../signal.php?action=get_answer&role=trainer&session_id=' + sessionId);
                const answers = await res.json();

                if (Array.isArray(answers)) {
                    for (const ans of answers) {
                        const desc = new RTCSessionDescription(ans);
                        await pc.setRemoteDescription(desc);
                    }
                    document.getElementById('status').innerText = 'Connected to all viewers.';
                } else {
                    setTimeout(pollForAnswer, 2000);
                }
            }


            screenStream.getVideoTracks()[0].addEventListener('ended', () => {
                document.getElementById('status').innerText = 'Screen sharing stopped.';
            });

            const micStream = await navigator.mediaDevices.getUserMedia({
                audio: true
            });

            // Add microphone audio tracks to the PeerConnection
            micStream.getAudioTracks().forEach(track => pc.addTrack(track, micStream));


        } catch (err) {
            alert('Could not start screen sharing: ' + err.message);
        }
    }

    async function pollForAnswer() {
        const res = await fetch('../signal.php?action=get_answer&role=trainer&session_id=' + sessionId);
        const answer = await res.json();

        if (answer && answer.type === 'answer') {
            document.getElementById('status').innerText = 'Answer received. Connecting...';
            await pc.setRemoteDescription(new RTCSessionDescription(answer));
            document.getElementById('status').innerText = 'Connected to viewer.';
        } else {
            setTimeout(pollForAnswer, 2000);
        }
    }

    document.getElementById('startShare').addEventListener('click', startScreenShare);
</script>

</div>
</body>

</html>