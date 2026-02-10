<?php
// Very simple file-based signaling (for learning/demo purposes)

$dir = __DIR__ . '/signaling';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$session_id = preg_replace('/[^0-9]/', '', ($_GET['session_id'] ?? $_POST['session_id'] ?? '0'));
$role = $_GET['role'] ?? $_POST['role'] ?? ''; // 'trainer' or 'viewer'

if (!$session_id || !$role) {
    http_response_code(400);
    echo "Missing session_id or role";
    exit;
}

$file_offer  = "$dir/offer_$session_id.json";
$file_answer = "$dir/answer_{$session_id}_" . uniqid() . ".json";

header('Content-Type: application/json');

if ($action === 'save_offer' && $role === 'trainer') {
    $body = file_get_contents('php://input');
    file_put_contents($file_offer, $body);
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'get_offer' && $role === 'viewer') {
    if (file_exists($file_offer)) {
        echo file_get_contents($file_offer);
    } else {
        echo json_encode(null);
    }
    exit;
}

if ($action === 'save_answer' && $role === 'viewer') {
    $body = file_get_contents('php://input');
    file_put_contents($file_answer, $body);
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($action === 'get_answer' && $role === 'trainer') {
    if (file_exists($file_answer)) {
        $answers = glob("$dir/answer_{$session_id}_*.json");
        $data = [];

        foreach ($answers as $file) {
            $data[] = json_decode(file_get_contents($file), true);
        }

        echo json_encode($data);
    } else {
        echo json_encode(null);
    }
    exit;
}

echo json_encode(['error' => 'Invalid action']);
