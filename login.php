<?php
session_start();
require __DIR__ . '/db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($usuario === "" || $password === "") {
        $mensaje = "Credenciales incorrectas";
    } else {
        // Buscar admin en BD
        $stmt = $db->prepare("SELECT id, usuario, password_hash FROM administradores WHERE usuario = :usuario LIMIT 1");
        $stmt->execute([':usuario' => $usuario]);
        $admin = $stmt->fetch();

        // Verificar hash
        if ($admin && password_verify($password, $admin["password_hash"])) {
            // Login OK: crear sesión y redirigir
            $_SESSION["admin_logged_in"] = true;
            $_SESSION["admin_usuario"] = $admin["usuario"];
            $_SESSION["admin_id"] = $admin["id"];

            header("Location: panel.php");
            exit;
        } else {
            $mensaje = "Credenciales incorrectas";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Acceso al sistema</h1>
</header>

<main>
    <form method="post" class="form-login" autocomplete="off">
        <label>Usuario</label>
        <input type="text" name="usuario" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Entrar</button>
        
    </form>

    <a href="index.html" class="btn-volver">Volver atrás</a>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2026 Museo Virtual</p>
</footer>

</body>
</html>
