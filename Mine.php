<?php
// Mine.php
require_once __DIR__ . "/config.php";

header("Content-Type: application/json; charset=UTF-8");

// Verifica autorização
$headers = getallheaders();
if (!isset($headers["Authorization"]) || $headers["Authorization"] !== "h") {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Lê JSON enviado
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

$db = getDB();

// Insere log principal
$stmt = $db->prepare("
    INSERT INTO logs (player, playerCount, maxPlayers, placeId, jobId, timestamp)
    VALUES (:player, :playerCount, :maxPlayers, :placeId, :jobId, :timestamp)
");
$stmt->execute([
    ":player" => $data["targetPlayer"] ?? "Unknown",
    ":playerCount" => $data["playerCount"] ?? 0,
    ":maxPlayers" => $data["maxPlayers"] ?? 0,
    ":placeId" => $data["placeId"] ?? "N/A",
    ":jobId" => $data["jobId"] ?? "N/A",
    ":timestamp" => $data["timestamp"] ?? date("Y-m-d H:i:s"),
]);
$logId = $db->lastInsertId();

// Insere pets vinculados
if (!empty($data["pets"])) {
    $petStmt = $db->prepare("
        INSERT INTO pets (log_id, name, fullName, rarity, price)
        VALUES (:log_id, :name, :fullName, :rarity, :price)
    ");
    foreach ($data["pets"] as $pet) {
        $petStmt->execute([
            ":log_id" => $logId,
            ":name" => $pet["name"] ?? "",
            ":fullName" => $pet["fullName"] ?? "",
            ":rarity" => $pet["rarity"] ?? "",
            ":price" => $pet["price"] ?? "",
        ]);
    }
}

echo json_encode([
    "status" => "success",
    "message" => "Data inserted successfully",
    "received_pets" => count($data["pets"] ?? [])
]);
?>
