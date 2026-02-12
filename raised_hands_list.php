<?php
include 'config/db.php';

$session_id = intval($_GET['session_id'] ?? 0);

$result = $conn->query("
    SELECT raise_hand.*, users.name 
    FROM raise_hand 
    JOIN users ON raise_hand.user_id = users.id
    WHERE session_id=$session_id
    ORDER BY raised_at DESC
");

$hands = [];
while ($row = $result->fetch_assoc()) {
    $hands[] = $row;
}

header('Content-Type: application/json');
echo json_encode($hands);
