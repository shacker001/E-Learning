<?php
include 'config/db.php';
session_start();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$session_id = intval($_GET['session_id'] ?? $_POST['session_id'] ?? 0);

if (!$session_id) {
    http_response_code(400);
    echo "Missing session_id";
    exit;
}

if ($action === 'send') {
    if (!isset($_SESSION['user'])) {
        http_response_code(403);
        exit;
    }
    $user_id = $_SESSION['user']['id'];
    $msg = trim($_POST['message'] ?? '');

    if ($msg !== '') {
        $stmt = $conn->prepare("INSERT INTO chat (session_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $session_id, $user_id, $msg);
        $stmt->execute();
    }
    echo "ok";
    exit;
}

if ($action === 'fetch') {
    $result = $conn->query("
        SELECT chat.*, users.name 
        FROM chat 
        JOIN users ON chat.user_id = users.id
        WHERE session_id = $session_id
        ORDER BY created_at ASC
    ");

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}

echo "Invalid action";
