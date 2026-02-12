<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Not logged in");
}

$session_id = intval($_POST['session_id'] ?? 0);
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("INSERT INTO raise_hand (session_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $session_id, $user_id);
$stmt->execute();

echo "Hand raised! Trainer will see your request.";
