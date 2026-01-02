<?php
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Museo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .panel-wrap { max-width: 1000px; margin: 0 auto; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: white; border: 1px solid #ddd; padding: 15px; }
        .form-row { display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
        .form-row input { padding: 8px; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; }
        .btn-primary { background: #222; color: #fff; }
        .btn-secondary { background: #666; color: #fff; }
        .btn-danger { background: #b00020; color: #fff; }
        .actions { display: flex; gap: 8px; justify-content: center; }
        .thumb { width: 80px; height: 60px; object-fit: cover; border: 1px solid #ddd; }
        .mensaje { margin-top: 10px; font-weight: bold; }
        table td, table th { vertical-align: middle; }
    </style>
</head>
<body>

<header>
    <h1>Panel de Control</h1>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, 'UTF-8'); ?></p>

    <form action="logout.php" method="post" style="margin-top:10px;">
    <button class="btn btn-danger" type="submit">Cerrar sesión</button>
</form>
</header>

<main class="panel-wrap">
    <div class="grid-2">
        <!-- FORMULARIO -->
        <section class="card">
            <h2>Añadir / Editar Obra</h2>

            <form id="obraForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">

                <div class="form-row">
                    <label for="titulo">Título</label>
                    <input type="text" name="titulo" id="titulo" required>
                </div>

                <div class="form-row">
                    <label for="autor">Autor</label>
                    <input type="text" name="autor" id="autor" required>
                </div>

                <div class="form-row">
                    <label for="imagen">Imagen (PNG/JPG/WebP) (opcional al editar)</label>
                    <input type="file" name="imagen" id="imagen" accept=".png,.jpg,.jpeg,.webp">
                </div>

                <button class="btn btn-primary" type="submit" id="btnSubmit">Guardar</button>
                <button class="btn btn-secondary" type="button" id="btnReset">Limpiar</button>

                <p class="mensaje" id="mensaje"></p>
            </form>
        </section>

        <!-- LISTADO -->
        <section class="card">
            <h2>Obras registradas</h2>

            <table id="tablaObras">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </section>
    </div>
</main>

<footer>
    <p>&copy; 2026 Museo Virtual</p>
</footer>

<script>
const tablaBody = document.querySelector("#tablaObras tbody");
const form = document.getElementById("obraForm");
const msg = document.getElementById("mensaje");
const btnReset = document.getElementById("btnReset");
const btnSubmit = document.getElementById("btnSubmit");

const fieldId = document.getElementById("id");
const fieldTitulo = document.getElementById("titulo");
const fieldAutor = document.getElementById("autor");
const fieldImagen = document.getElementById("imagen");

function setMensaje(texto, ok=true){
  msg.textContent = texto;
  msg.style.color = ok ? "green" : "crimson";
}

function limpiarForm(){
  fieldId.value = "";
  fieldTitulo.value = "";
  fieldAutor.value = "";
  fieldImagen.value = "";
  btnSubmit.textContent = "Guardar";
  setMensaje("");
}

async function cargarObras(){
  const res = await fetch("get_obras.php");
  const obras = await res.json();

  tablaBody.innerHTML = "";
  obras.forEach(o => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${o.id}</td>
      <td><img class="thumb" src="img/${o.imagen}" alt="${o.titulo}"></td>
      <td>${o.titulo}</td>
      <td>${o.autor}</td>
      <td class="actions">
        <button class="btn btn-secondary" data-edit="${o.id}">Editar</button>
        <button class="btn btn-danger" data-del="${o.id}">Eliminar</button>
      </td>
    `;
    tablaBody.appendChild(tr);
  });
}

tablaBody.addEventListener("click", async (e) => {
  const editId = e.target.getAttribute("data-edit");
  const delId  = e.target.getAttribute("data-del");

  if (editId) {
    const res = await fetch(`get_obra.php?id=${encodeURIComponent(editId)}`);
    const obra = await res.json();

    fieldId.value = obra.id;
    fieldTitulo.value = obra.titulo;
    fieldAutor.value = obra.autor;
    fieldImagen.value = "";
    btnSubmit.textContent = "Actualizar";
    setMensaje("Editando obra ID " + obra.id, true);
  }

  if (delId) {
    if (!confirm("¿Seguro que quieres eliminar esta obra?")) return;

    const res = await fetch("delete_obra.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: delId })
    });

    const data = await res.json();
    setMensaje(data.message, data.ok);

    if (data.ok) {
      limpiarForm();
      await cargarObras();
    }
  }
});

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const fd = new FormData(form);

  const res = await fetch("save_obra.php", {
    method: "POST",
    body: fd
  });

  const data = await res.json();
  setMensaje(data.message, data.ok);

  if (data.ok) {
    limpiarForm();
    await cargarObras();
  }
});

btnReset.addEventListener("click", () => limpiarForm());

cargarObras().catch(() => setMensaje("Error cargando obras", false));
</script>

</body>
</html>

