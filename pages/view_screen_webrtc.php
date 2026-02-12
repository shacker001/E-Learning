<?php
include '../config/db.php';
include '../header.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$session_id = intval($_GET['session_id'] ?? 1);
$user_id = $_SESSION['user']['id'];

// $stmt = $conn->prepare("INSERT INTO attendance (user_id, session_id) VALUES (?, ?)");
// $stmt->bind_param("ii", $user_id, $session_id);
// $stmt->execute();

$stmt = $conn->prepare("UPDATE attendance SET last_seen = NOW() WHERE user_id=? AND session_id=?");
$stmt->bind_param("ii", $user_id, $session_id);
$stmt->execute();

?>


<h3>View Trainer Screen</h3>
<p><strong>Session ID:</strong> <?php echo $session_id; ?></p>

<div class="row">
    <div class="col-md-8">
        <video id="remoteVideo" autoplay playsinline class="border" style="width:100%; max-height:70vh;"></video>
        <div id="status" class="text-muted mt-3"></div>
    </div>

    <div class="col-md-4">
        <div class="card" style="height:70vh; display:flex; flex-direction:column;">
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

    <button id="raiseHandBtn" class="btn btn-warning mt-3">
        <i class="bi bi-hand-index-thumb"></i> Raise Hand
    </button>

    <div id="raiseStatus" class="text-muted mt-2"></div>

</div>


<script>
    const sessionId = <?php echo $session_id; ?>;
    let pc;

    async function startViewing() {
        document.getElementById('status').innerText = 'Waiting for trainer offer...';

        const res = await fetch('../signal.php?action=get_offer&role=viewer&session_id=' + sessionId);
        const offer = await res.json();

        if (!offer || !offer.type) {
            document.getElementById('status').innerText = 'No offer yet. Waiting...';
            setTimeout(startViewing, 3000);
            return;
        }

        pc = new RTCPeerConnection({
            iceServers: [{
                urls: 'stun:stun.l.google.com:19302'
            }]
        });

        pc.ontrack = (event) => {
            document.getElementById('remoteVideo').srcObject = event.streams[0];
        };

        await pc.setRemoteDescription(new RTCSessionDescription(offer));

        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);

        document.getElementById('status').innerText = 'Sending answer to server...';

        await fetch('../signal.php?action=save_answer&role=viewer&session_id=' + sessionId, {
            method: 'POST',
            body: JSON.stringify(answer),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        document.getElementById('status').innerText = 'Connected. You should see the trainer screen.';
    }

    startViewing();
</script>
<script>
    const sessionIdChat = <?php echo $session_id; ?>;

    async function fetchChat() {
        const res = await fetch('../chat_handler.php?action=fetch&session_id=' + sessionIdChat);
        const data = await res.json();
        const box = document.getElementById('chatBox');
        box.innerHTML = '';

        data.forEach(msg => {
            const div = document.createElement('div');
            div.className = 'mb-1';
            div.innerHTML = '<strong>' + msg.name + ':</strong> ' + msg.message +
                '<br><small class="text-muted">' + msg.created_at + '</small>';
            box.appendChild(div);
        });

        box.scrollTop = box.scrollHeight;
    }

    document.getElementById('chatForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('chatMessage');
        const text = input.value.trim();
        if (!text) return;

        await fetch('../chat_handler.php', {
            method: 'POST',
            body: new URLSearchParams({
                action: 'send',
                session_id: sessionIdChat,
                message: text
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });

        input.value = '';
        fetchChat();
    });

    setInterval(fetchChat, 2000);
    fetchChat();
</script>

<script>
    setInterval(async () => {
        await fetch('../update_presence.php?session_id=<?php echo $session_id; ?>');
    }, 30000);
</script>

<script>
    document.getElementById('raiseHandBtn').addEventListener('click', async () => {
        const res = await fetch('../raise_hand.php', {
            method: 'POST',
            body: new URLSearchParams({
                session_id: <?php echo $session_id; ?>
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        const text = await res.text();
        document.getElementById('raiseStatus').innerText = text;
    });
</script>


</div>
</body>

</html>