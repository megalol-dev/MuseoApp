<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Traer todas las obras ordenadas por ID
    $stmt = $db->query("SELECT id, titulo, autor, imagen FROM obras ORDER BY id ASC");
    $obras = $stmt->fetchAll();

    echo json_encode($obras, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al obtener las obras",
        "detalle" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
