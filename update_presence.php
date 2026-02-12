<?php
include 'config/db.php';
session_start();

$session_id = intval($_GET['session_id'] ?? 0);
$user_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("UPDATE attendance SET last_seen = NOW() WHERE user_id=? AND session_id=?");
$stmt->bind_param("ii", $user_id, $session_id);
$stmt->execute();
