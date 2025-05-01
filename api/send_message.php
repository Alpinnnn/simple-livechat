<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->message)) {
    // Tentukan jenis pengirim pesan (1 = user, 0 = admin)
    $is_user = isset($data->is_admin) && $data->is_admin === true ? 0 : 1;
    
    $query = "INSERT INTO messages (message, is_user, created_at) VALUES (:message, :is_user, NOW())";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":message", $data->message);
    $stmt->bindParam(":is_user", $is_user, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Pesan berhasil dikirim.", "success" => true));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Tidak dapat mengirim pesan.", "success" => false));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Tidak dapat mengirim pesan. Data tidak lengkap.", "success" => false));
}
?> 