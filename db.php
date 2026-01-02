<?php
try {
    // Ruta a la base de datos SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/museo.db');

    // Configuración recomendada
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión con la base de datos: " . $e->getMessage());
}
