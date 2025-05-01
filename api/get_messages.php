<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, message, is_user, created_at FROM messages ORDER BY created_at ASC";
$stmt = $db->prepare($query);
$stmt->execute();

$messages = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message_item = array(
        "id" => $row['id'],
        "message" => $row['message'],
        "is_user" => $row['is_user'],
        "created_at" => $row['created_at']
    );
    array_push($messages, $message_item);
}

echo json_encode(array("messages" => $messages));
?> 