<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$id = isset($data['id']) ? (int)$data['id'] : 0;

if ($id <= 0) {
    echo json_encode(["ok" => false, "message" => "ID inválido"]);
    exit;
}

try {
    // (Opcional) si quieres borrar también el archivo de imagen:
    // - primero leemos la imagen
    $stmt = $db->prepare("SELECT imagen FROM obras WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $obra = $stmt->fetch();

    if (!$obra) {
        echo json_encode(["ok" => false, "message" => "Obra no encontrada"]);
        exit;
    }

    // borrar registro
    $stmt = $db->prepare("DELETE FROM obras WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // borrar archivo (solo si existe y no es una de las imágenes “base” si quieres protegerlas)
    $img = $obra['imagen'];
    $ruta = __DIR__ . '/img/' . $img;
    if (is_file($ruta)) {
        @unlink($ruta);
    }

    echo json_encode(["ok" => true, "message" => "Obra eliminada correctamente."]);

} catch (PDOException $e) {
    echo json_encode(["ok" => false, "message" => "Error en BD: " . $e->getMessage()]);
}
