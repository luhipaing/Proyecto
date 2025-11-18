<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Acceso denegado');
}

// Registrar jornada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['work_date'])) {
    $date = $_POST['work_date'] ?? '';
    $hours = $_POST['horas'] ?? 0;
    $description = $_POST['descripcion'] ?? '';

    try {
        $stmt = $pdo->prepare('INSERT INTO horastrabajo (user_id, work_date, horas, descripcion) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $date, $hours, $description]);
        $_SESSION['mensaje'] = " Jornada registrada correctamente";
        $_SESSION['tipo'] = "exito";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['mensaje'] = " Ya tienes una jornada registrada en esa fecha.";
            $_SESSION['tipo'] = "advertencia";
        } else {
            $_SESSION['mensaje'] = " Error al registrar la jornada.";
            $_SESSION['tipo'] = "error";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



/// Obtener comprobantes
$stmt = $pdo->prepare("SELECT idPago, fecha_subida, estado, archivo 
                       FROM pagos 
                       WHERE id_usuario = ?");
$stmt->execute([$_SESSION['user_id']]);
$comprobantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE idUser = ?");
$stmt->execute([$_SESSION['user_id']]);
$foto = $stmt->fetchColumn();
if (empty($foto)) {
    $foto = 'default.jpg';
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel del Usuario</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f5f6f8; }
    .navbar {
      background-color: rgb(0, 162, 255);
      color: white;
      padding: 0.8rem 1rem;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      height: 80px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .menu-icon {
      position: absolute; 
      left: 1rem;
      font-size: 28px; 
      cursor: pointer;
    }
    .logo img { width: 150px; height: auto; object-fit: contain; }
    .profile-pic, .profile-default {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 2px solid white;
      cursor: pointer;
    }
    .profile-pic { object-fit: cover; }
    .profile-default {
      background: radial-gradient(circle at 50% 35%, white 25%, transparent 26%), 
                  radial-gradient(circle at 50% 75%, white 40%, transparent 41%), 
                  #999;
      background-repeat: no-repeat;
      background-size: 70% 70%, 90% 90%;
      background-position: center;
    }
    .sidebar {
      position: fixed;
      top: 0; 
      left: -250px;
      width: 250px; 
      height: 100%;
      background: rgb(0, 162, 255);
      padding-top: 60px; 
      transition: left 0.3s ease-in-out;
      display: flex; 
      flex-direction: column;
      z-index: 1001;
    } 
    .sidebar.active { left: 0; }
    .sidebar a, .jornadasb {
      padding: 15px;
      text-decoration: none;
      color: white;
      display: block;
      border-bottom: 1px solid #555;
      background: none;
      border: none;
      text-align: left;
      cursor: pointer;
      font: inherit;
      transition: background 0.25s ease, padding-left 0.25s ease;
    }
    .sidebar a:hover, .jornadasb:hover {
      background: #1e90ff;
      padding-left: 25px;
    }
    .overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      z-index: 1000;
    }
    .overlay.active { display: block; }
    /* ===== Estilos para Jornadas (modal y formulario) ===== */
#jornadaModal {
  display: none; /* se muestra con JS: flex */
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.55);
  z-index: 2500;
  justify-content: center;
  align-items: center;
}

#jornadaModal.active {
  display: flex;
}

.jornada-card {
  background: #ffffff;
  width: 420px;
  max-width: calc(100% - 40px);
  border-radius: 12px;
  padding: 22px;
  box-shadow: 0 6px 20px rgba(3, 37, 76, 0.12);
  text-align: left;
}

/* Header */
.jornada-card h2 {
  margin: 0 0 12px 0;
  color: rgb(0,162,255);
  font-size: 20px;
}

/* Form controls */
.jornada-card label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
  color: #333;
  font-size: 13px;
}

.jornada-card input[type="date"],
.jornada-card input[type="number"],
.jornada-card textarea {
  width: 100%;
  padding: 9px 10px;
  margin-top: 6px;
  border: 1px solid #d7dbe0;
  border-radius: 8px;
  background: #fafafa;
  font-size: 14px;
  box-sizing: border-box;
}

.jornada-card textarea { resize: vertical; min-height: 80px; }

/* Buttons */
.jornada-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 16px;
}

.btn-primary {
  background: rgb(0,162,255);
  color: #fff;
  border: none;
  padding: 9px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 700;
  transition: background .15s ease;
}

.btn-primary:hover { background: #007fcf; }

.btn-ghost {
  background: transparent;
  color: #333;
  border: 1px solid #d7dbe0;
  padding: 9px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.jornada-msg {
  margin-top: 10px;
  padding: 10px;
  border-radius: 8px;
  font-weight: 600;
  display: none;
}

.jornada-msg.success { background: #e6fbf1; color: #0f8a44; }
.jornada-msg.error   { background: #fff0f0; color: #c33; }

/* Accesibilidad: foco claro */
.jornada-card input:focus,
.jornada-card textarea:focus,
.jornada-card button:focus {
  outline: 3px solid rgba(0,162,255,0.15);
  outline-offset: 2px;
}

    .modal-content {
      background: #fff;
      margin: 5% auto;
      padding: 20px;
      width: 400px;
      border-radius: 10px;
      text-align: center;
    }
    #modalMsg {
      position: fixed; 
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      display: flex; 
      justify-content: center; 
      align-items: center;
      z-index: 3000;
    }
    #modalMsg .msg-box {
      background: #fff;
      padding: 25px 40px;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
    }
    #modalMsg button {
      margin-top: 15px; 
      padding: 10px 20px;
      border: none; 
      background: #007bff; 
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
    }
    #seccionComprobantes {
      display: none;
      margin: 30px auto;
      width: 80%;
      max-width: 700px;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
  
#seccionComprobantes {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  width: 80%;
  margin: 40px auto;
  animation: fadeIn 0.3s ease-in-out;
}

#seccionComprobantes h2, 
#seccionComprobantes h3 {
  color: rgb(0, 162, 255);
  text-align: center;
  margin-bottom: 20px;
}

#seccionComprobantes form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: center;
}

#seccionComprobantes label {
  font-weight: 600;
  color: #333;
}

#seccionComprobantes select,
#seccionComprobantes input[type="file"],
#seccionComprobantes button {
  width: 60%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 14px;
}

#seccionComprobantes button {
  background: rgb(0, 162, 255);
  color: white;
  font-weight: bold;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

#seccionComprobantes button:hover {
  background: #008fe0;
}

#seccionComprobantes table {
  margin: 20px auto;
  border-collapse: collapse;
  width: 90%;
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

#seccionComprobantes th {
  background: rgb(0, 162, 255);
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 10px;
}

#seccionComprobantes td {
  padding: 10px;
  text-align: center;
  border-bottom: 1px solid #eee;
}

#seccionComprobantes tr:hover {
  background: #f0f9ff;
}

#seccionComprobantes a {
  color: rgb(0, 162, 255);
  text-decoration: none;
  font-weight: 600;
}

#seccionComprobantes a:hover {
  text-decoration: underline;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

#jornadaModal {
  display: none; 
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6); 
  z-index: 2000;
  justify-content: center;
  align-items: center;
  text-align: center;
}


#jornadaModal .modal-content {
  position: relative;
  background: #fff;
  width: 400px;
  max-width: 90%;
  margin: 5% auto;
  padding: 25px 30px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  text-align: left;
  animation: aparecer 0.3s ease-out;
}

@keyframes aparecer {
  from { transform: translateY(-20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

#jornadaModal h1 {
  text-align: center;
  color: rgb(0, 162, 255);
  font-size: 20px;
  margin-bottom: 15px;
}

#jornadaModal label {
  display: block;
  margin-top: 10px;
  color: #333;
  font-weight: 600;
}

#jornadaModal input,
#jornadaModal textarea {
  width: 100%;
  padding: 8px 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}

#jornadaModal textarea {
  resize: vertical;
  min-height: 70px;
}

#jornadaModal button[type="submit"] {
  background: rgb(0, 162, 255);
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  margin-top: 15px;
  cursor: pointer;
  font-weight: 600;
}

#jornadaModal button[type="submit"]:hover {
  background: #007fcf;
}

#jornadaModal button[type="button"] {
  background: #f3f3f3;
  border: 1px solid #ccc;
  padding: 8px 14px;
  border-radius: 6px;
  margin-left: 10px;
  cursor: pointer;
}

#jornadaModal button[type="button"]:hover {
  background: #e4e4e4;
}

#modalUnidad {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.55);
  z-index: 3000;
  justify-content: center;
  align-items: center;
  animation: fadeInModal 0.3s ease-in-out;
}

#modalUnidad .modal-content {
  background: #fff;
  border-radius: 14px;
  padding: 25px 30px;
  width: 420px;
  max-width: 90%;
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  position: relative;
  text-align: left;
}

#modalUnidad .close {
  position: absolute;
  top: 12px;
  right: 15px;
  font-size: 22px;
  color: #333;
  cursor: pointer;
  transition: color 0.2s ease;
}

#modalUnidad .close:hover {
  color: rgb(0, 162, 255);
}

#modalUnidad h2 {
  color: rgb(0, 162, 255);
  text-align: center;
  margin-bottom: 15px;
}

.unidad-card {
  background: #f9fbff;
  border: 1px solid #e1e9f5;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 2px 8px rgba(0,162,255,0.1);
}

.unidad-card h3 {
  color: rgb(0, 162, 255);
  margin-top: 0;
}

.unidad-card p {
  margin: 6px 0;
  color: #333;
  font-size: 14px;
}

@keyframes fadeInModal {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.reporte-card {
  background: white;
  border-radius: 10px;
  padding: 15px 20px;
  margin: 15px auto;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  max-width: 600px;
  transition: transform 0.2s ease;
}
.reporte-card:hover {
  transform: scale(1.02);
}
.reporte-card h4 {
  margin: 0;
  color: #007BFF;
}
.reporte-card p {
  margin-top: 8px;
  color: #333;
}




.avisos-feed {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.aviso-card {
    background: #fff;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.aviso-card h3 {
    margin: 0;
    font-size: 1.5rem;
}

.meta {
    color: gray;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.aviso-contenido {
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.comentario-card {
    background: #f1f1f1;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.form-comentario textarea {
    width: 100%;
    height: 70px;
    resize: none;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.form-comentario button {
    background: #0056b3;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
}

.form-comentario button:hover {
    background: #003d80;
}
.comentarios-container {
    display: none;
    margin-top: 10px;
}

.toggle-btn {
    background: #555;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    margin-bottom: 8px;
}
.toggle-btn:hover {
    background: #333;
}


  </style>

  
   <script>
function toggleMenu() {
  document.getElementById("sidebar").classList.toggle("active");
  document.getElementById("overlay").classList.toggle("active");
}

function cerrarModalMsg() {
  document.getElementById('modalMsg').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", () => {
  // --- Botón Comprobantes ---
  const btnComprobantes = document.getElementById("btnComprobantes");
  if (btnComprobantes) {
    btnComprobantes.addEventListener("click", () => {
      const seccion = document.getElementById("seccionComprobantes");
      seccion.style.display = (seccion.style.display === "none" || seccion.style.display === "") 
        ? "block" 
        : "none";
    });
  }

  // --- Botón Unidad Habitacional ---
  const btnUnidad = document.getElementById("btnUnidadHabitacional");
  const modalUnidad = document.getElementById("modalUnidad");
  const contenidoUnidad = document.getElementById("contenidoUnidad");

  if (btnUnidad && modalUnidad) {
    btnUnidad.addEventListener("click", () => {
      modalUnidad.style.display = "flex";
      contenidoUnidad.innerHTML = "<p>Cargando información...</p>";

      fetch("backend_unidad.php")
        .then(res => res.text())
        .then(html => contenidoUnidad.innerHTML = html)
        .catch(() => contenidoUnidad.innerHTML = "<p>Error al cargar la información.</p>");
    });
  }

  // Cerrar el modal al hacer clic fuera
  window.addEventListener("click", (event) => {
    if (event.target === modalUnidad) modalUnidad.style.display = "none";
  });
});

function cerrarUnidadModal() {
  document.getElementById("modalUnidad").style.display = "none";
}
function toggleComentarios(id) {
    const cont = document.getElementById("comentarios-" + id);
    const btn = document.getElementById("btn-" + id);

    if (!cont) {
        console.error("No existe el contenedor comentarios-" + id);
        return;
    }

    if (cont.style.display === "none" || cont.style.display === "") {
        cont.style.display = "block";
        btn.textContent = "Ocultar comentarios";
    } else {
        cont.style.display = "none";
        btn.textContent = "Ver comentarios";
    }
}
</script>


  
</head>
<body>
  <div class="navbar">
    <span class="menu-icon" onclick="toggleMenu()">&#9776;</span>
    <a href="frontend.php">
    <div class="logo"><img src="logo.png" alt="Logo"></div></a>
    <a href="perfil.php">
      <?php if ($foto !== 'default.jpg'): ?>
        <img src="<?php echo htmlspecialchars($foto); ?>" alt="Perfil" class="profile-pic">
      <?php else: ?>
        <div class="profile-default"></div>
      <?php endif; ?>
    </a>
  </div>


<h2 style="margin-bottom: 20px;"> Avisos de la Cooperativa</h2>

<div class="avisos-feed">

<?php
// Traer todos los avisos
$stmt = $pdo->query("
    SELECT a.idAviso, a.titulo, a.contenido, a.fecha, u.nombre
    FROM avisos a
    JOIN usuarios u ON u.idUser = a.idUser
    ORDER BY a.fecha DESC
");

$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($avisos as $a):
?>

<div class="aviso-card">

    <h3><?= htmlspecialchars($a['titulo']) ?></h3>
    <p class="meta">
        Publicado por <?= htmlspecialchars($a['nombre']) ?> — <?= htmlspecialchars($a['fecha']) ?>
    </p>

    <p class="aviso-contenido"><?= nl2br(htmlspecialchars($a['contenido'])) ?></p>

    <hr>

    <button id="btn-<?= $a['idAviso'] ?>" class="toggle-btn" onclick="toggleComentarios(<?= $a['idAviso'] ?>)">
    Ver comentarios
</button>

<div id="comentarios-<?= $a['idAviso'] ?>" class="comentarios-container">
    <h4> Comentarios</h4>




<?php
$stmt2 = $pdo->prepare("
    SELECT c.comentario, c.fecha_comentario, u.nombre
    FROM comentarios c
    JOIN usuarios u ON u.idUser = c.idUser
    WHERE c.idAviso = ?
    ORDER BY c.fecha_comentario ASC
");
$stmt2->execute([$a['idAviso']]);
$comentarios = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if ($comentarios):
    foreach ($comentarios as $c):
?>
    <div class="comentario-card">
        <p><strong><?= htmlspecialchars($c['nombre']) ?></strong></p>
        <p><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
        <small><?= htmlspecialchars($c['fecha_comentario']) ?></small>
    </div>
<?php
    endforeach;
else:
?>
    <p style="color:gray;">No hay comentarios todavía</p>
<?php endif; ?>

<form class="form-comentario" action="guardarComentario.php" method="POST">
    <input type="hidden" name="idAviso" value="<?= $a['idAviso'] ?>">
    <textarea name="comentario" required></textarea>
    <button type="submit">Comentar</button>
</form>

</div>


<?php endforeach; ?>

</div>

</div>
  <div class="sidebar" id="sidebar">
    <button id="btnComprobantes" type="button" class="jornadasb">Comprobantes</button>
    <button class="jornadasb" type="button" onclick="document.getElementById('jornadaModal').style.display='block'">
      Jornadas
    </button>
    <button id="btnUnidadHabitacional" type="button" class="jornadasb">Unidad Habitacional</button>
   <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
  
    <a href="backoffice.php" id="adminBtn" class="admin-btn">Admin</a>
  
<?php endif; ?>

  </div>

  <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

  <!-- Modal Jornada -->
  <div id="jornadaModal">
    <div class="modal-content">
      <h1><?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
      <form method="post">
        <label>Fecha<br><input type="date" name="work_date" required></label><br>
        <label>Horas trabajadas<br><input type="number" name="horas" step="0.1" required></label><br>
        <label>Descripción<br><textarea name="descripcion"></textarea></label><br>
        <button type="submit">Registrar</button>
        <button type="button" onclick="document.getElementById('jornadaModal').style.display='none'">Cerrar</button>
      </form>
    </div>
  </div>
<!-- MODAL UNIDAD HABITACIONAL -->
<div id="modalUnidad" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="cerrarUnidadModal()">&times;</span>
    <h2>Mi Unidad Habitacional</h2>
    <div id="contenidoUnidad">
      <!-- Aquí se cargará dinámicamente el contenido -->
      <p>Cargando información...</p>
    </div>
  </div>
</div>

  <!-- Sección Comprobantes -->
  <!-- Sección Comprobantes -->
<div id="seccionComprobantes" style="display:none; margin-top:20px;">
  <h2> Subir comprobante de pago</h2>

  <form action="procesar_comprobante.php" method="POST" enctype="multipart/form-data">
    <label>Tipo de comprobante:</label>
    <select name="tipo" required>
        <option value="pago">Pago</option>
        <option value="certificado">Certificado</option>
        <option value="eximido">Eximido de horas laborales</option>
    </select>
    <br><br>
    <label>Seleccionar archivo (PDF o imagen):</label>
    <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
    <br><br>
    <button type="submit">Subir comprobante</button>
  </form>

  <hr>

  <h3> Mis comprobantes</h3>
  <?php
  try {
      $id_usuario = $_SESSION['user_id']; // usamos la sesión actual
      $stmt = $pdo->prepare("SELECT tipo, archivo, estado, fecha_subida 
                             FROM pagos 
                             WHERE id_usuario = ? 
                             ORDER BY fecha_subida DESC");
      $stmt->execute([$id_usuario]);
      $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($pagos) {
          echo "<table border='1' cellpadding='5' cellspacing='0'>
                  <tr><th>Tipo</th><th>Archivo</th><th>Estado</th><th>Fecha</th></tr>";
          foreach ($pagos as $row) {
              echo "<tr>";
              echo "<td>" . ucfirst(htmlspecialchars($row['tipo'])) . "</td>";
              echo "<td><a href='" . htmlspecialchars($row['archivo']) . "' target='_blank'>Ver</a></td>";
              echo "<td>" . ucfirst(htmlspecialchars($row['estado'])) . "</td>";
              echo "<td>" . htmlspecialchars($row['fecha_subida']) . "</td>";
              echo "</tr>";
          }
          echo "</table>";
      } else {
          echo "<p>No subiste ningún comprobante aún.</p>";
      }
  } catch (PDOException $e) {
      echo "<p style='color:red;'>Error al obtener comprobantes: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>
</div>


  <?php if (isset($_SESSION['mensaje'])): ?>
  <div id="modalMsg">
    <div class="msg-box">
      <h2 style="color:
        <?php echo $_SESSION['tipo'] === 'exito' ? 'green' :
                ($_SESSION['tipo'] === 'advertencia' ? 'orange' : 'red'); ?>">
        <?php echo $_SESSION['mensaje']; ?>
      </h2>
      <button onclick="cerrarModalMsg()">Cerrar</button>
    </div>
  </div>
  <?php unset($_SESSION['mensaje'], $_SESSION['tipo']); endif; ?>
</body>
</html>
