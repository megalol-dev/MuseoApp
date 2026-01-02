<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["ok" => false, "message" => "ID inválido"]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT id, titulo, autor, imagen FROM obras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $obra = $stmt->fetch();

    if (!$obra) {
        http_response_code(404);
        echo json_encode(["ok" => false, "message" => "Obra no encontrada"]);
        exit;
    }

    echo json_encode($obra, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "message" => "Error en BD", "detalle" => $e->getMessage()]);
}

