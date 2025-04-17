<?php

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data && isset($data['reservationId'])) {
    $reservationId = $data['reservationId'];

    $db->query("UPDATE reservations SET status = 'confirmed' WHERE id = $reservationId");

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
