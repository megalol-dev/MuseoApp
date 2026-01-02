<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

function respuesta($ok, $message) {
    echo json_encode(["ok" => $ok, "message" => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['titulo'] ?? '');
$autor  = trim($_POST['autor'] ?? '');

if ($titulo === '' || $autor === '') {
    respuesta(false, "Título y autor son obligatorios.");
}

$nombreImagenFinal = null;

// Si viene archivo, lo validamos y lo guardamos en /img
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        respuesta(false, "Error subiendo la imagen.");
    }

    $tmp  = $_FILES['imagen']['tmp_name'];
    $name = $_FILES['imagen']['name'] ?? '';
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    $permitidas = ['png', 'jpg', 'jpeg', 'webp'];
    if (!in_array($ext, $permitidas, true)) {
        respuesta(false, "Formato no permitido. Usa PNG/JPG/WebP.");
    }

    // Nombre único para evitar sobrescribir
    $nombreImagenFinal = 'obra_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $destinoDir = __DIR__ . '/img';
    $destino = $destinoDir . '/' . $nombreImagenFinal;

    if (!is_dir($destinoDir)) {
        respuesta(false, "No existe la carpeta img/.");
    }

    if (!move_uploaded_file($tmp, $destino)) {
        respuesta(false, "No se pudo guardar la imagen en img/.");
    }
}

try {
    if ($id > 0) {
        // UPDATE: si no sube imagen, mantenemos la anterior
        if ($nombreImagenFinal === null) {
            $stmt = $db->prepare("UPDATE obras SET titulo = :titulo, autor = :autor WHERE id = :id");
            $stmt->execute([':titulo' => $titulo, ':autor' => $autor, ':id' => $id]);
        } else {
            $stmt = $db->prepare("UPDATE obras SET titulo = :titulo, autor = :autor, imagen = :imagen WHERE id = :id");
            $stmt->execute([':titulo' => $titulo, ':autor' => $autor, ':imagen' => $nombreImagenFinal, ':id' => $id]);
        }

        respuesta(true, "Obra actualizada correctamente.");
    } else {
        // INSERT: aquí sí exigimos imagen (si quieres, lo hacemos opcional, pero normalmente se pide)
        if ($nombreImagenFinal === null) {
            respuesta(false, "Para crear una obra nueva debes subir una imagen.");
        }

        $stmt = $db->prepare("INSERT INTO obras (titulo, autor, imagen) VALUES (:titulo, :autor, :imagen)");
        $stmt->execute([':titulo' => $titulo, ':autor' => $autor, ':imagen' => $nombreImagenFinal]);

        respuesta(true, "Obra añadida correctamente.");
    }

} catch (PDOException $e) {
    respuesta(false, "Error en BD: " . $e->getMessage());
}
