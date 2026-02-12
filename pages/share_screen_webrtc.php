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
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card" style="height:60vh; display:flex; flex-direction:column;">
            <div class="card-header">
                <strong>Class Chat</strong>
            </div>
            <div id="chatBox" class="card-body" style="overflow-y:auto; flex:1; font-size:0.9rem;"></div>
            <div class="card-footer">
                <form id="chatForm">
                    <div class="input-group">
                        <input type="text" id="chatMessage" class="form-control" placeholder="Type a message...">
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <strong>Raised Hands</strong>
    </div>
    <div id="raisedHandsBox" class="card-body" style="max-height:200px; overflow-y:auto;"></div>
</div>


<div id="onlineCount" class="mt-3 text-success"></div>

<script>
    async function fetchOnlineCount() {
        const res = await fetch('../online_count.php?session_id=<?php echo $session_id; ?>');
        const data = await res.json();
        document.getElementById('onlineCount').innerText = "Online students: " + data.count;
    }
    setInterval(fetchOnlineCount, 5000);
    fetchOnlineCount();
</script>


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

<script>
    async function fetchRaisedHands() {
        const res = await fetch('../raised_hands_list.php?session_id=<?php echo $session_id; ?>');
        const data = await res.json();
        const box = document.getElementById('raisedHandsBox');
        box.innerHTML = '';

        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'mb-1';
            div.innerHTML = '<i class="bi bi-hand-index-thumb text-warning"></i> ' +
                item.name + ' <small class="text-muted">(' + item.raised_at + ')</small>';
            box.appendChild(div);
        });
    }
    setInterval(fetchRaisedHands, 5000);
    fetchRaisedHands();
</script>


</div>
</body>

</html>