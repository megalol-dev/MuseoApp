# Museo Virtual – Proyecto DAW

Aplicación web sencilla para la gestión de un museo.  
Permite visualizar obras de arte y administrar el inventario mediante un panel privado con sistema CRUD.

Proyecto desarrollado como práctica del ciclo **DAW**, utilizando PHP, SQLite y JavaScript.

---

## 🚀 Funcionalidades

### Zona pública
- Visualización de las obras de arte en formato galería
- Carga dinámica de datos mediante Fetch (API REST)

### Zona privada (panel de control)
- Inicio de sesión con usuario y contraseña
- Gestión completa de obras:
  - Añadir obras
  - Editar obras
  - Eliminar obras
- Subida de imágenes desde el equipo
- Cierre de sesión seguro

---

## 🛠️ Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript (Fetch API)
- PHP
- SQLite
- Servidor integrado de PHP

---

## 📂 Estructura del proyecto

MuseoApp/
├── index.html
├── style.css
├── login.php
├── logout.php
├── panel.php
├── db.php
├── museo.db
├── get_obras.php
├── get_obra.php
├── save_obra.php
├── delete_obra.php
└── img/

--

## ▶️ Cómo ejecutar el proyecto

1. Clona el repositorio:
   ```bash
   git clone https://github.com/megalol-dev/MuseoApp.git
   
   Entra en la carpeta del proyecto: cd MuseoApp

   Inicia el servidor PHP: php -S localhost:8000
  
   Abre en el navegador: http://localhost:8000/index.html

--

🔐 Credenciales de acceso
Usuario: admin
Contraseña: admin123

Las contraseñas se almacenan de forma segura mediante hash.

--
Nota -> dentro del proyecto tienes una carpeta llamada documentación, dentro puedes obtener toda la información que necesitas. 
