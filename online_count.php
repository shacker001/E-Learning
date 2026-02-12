<?php
include 'config/db.php';

$session_id = intval($_GET['session_id'] ?? 0);

// consider "online" if last_seen within 1 minute
$result = $conn->query("SELECT COUNT(*) AS cnt FROM attendance WHERE session_id=$session_id AND last_seen > NOW() - INTERVAL 1 MINUTE");
$row = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode(['count' => $row['cnt']]);
