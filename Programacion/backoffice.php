<?php
session_start();
require 'db.php';

// Verificar si el usuario tiene rol admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
   header("Location: loadingpage.php");
   exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Backoffice</title>
<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f8;
    margin: 0;
    padding: 20px;
    color: #333;
  }

  h1, h2 {
    text-align: center;
    color: rgb(0, 162, 255);
    margin-bottom: 15px;
  }

  table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }

  th {
    background: rgb(0, 162, 255);
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px;
  }

  td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
  }

  tr:hover {
    background: #f0f9ff;
  }

  a {
    text-decoration: none;
    color: rgb(0, 162, 255);
    font-weight: 600;
  }

  a:hover {
    text-decoration: underline;
  }

  p {
    text-align: center;
    color: #777;
  }
</style>
</head>
<body>
<!-- tu contenido PHP/HTML -->
</body>
</html>

<?php
require 'db.php';

// --- Gestión de usuarios pendientes ---
if (isset($_GET['action'], $_GET['idUser'])) {
    $idUser = (int) $_GET['idUser'];
    if ($_GET['action'] === 'approve') {
        $pdo->prepare('UPDATE usuarios SET status="active" WHERE idUser=?')->execute([$idUser]);
    } elseif ($_GET['action'] === 'deny') {
        $pdo->prepare('UPDATE usuarios SET status="denied" WHERE idUser=?')->execute([$idUser]);
    }
}

$pending = $pdo->query("SELECT * FROM usuarios WHERE status='pending'")->fetchAll();
$horas = $pdo->query("
    SELECT w.*, u.nombre, u.idUser 
    FROM horastrabajo w 
    JOIN usuarios u ON u.idUser = w.user_id 
    ORDER BY w.created_at DESC
")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Backoffice</title>
</head>
<body>
  <form action="frontend.php" method="GET" style="margin-bottom:15px;">
    <button type="submit" 
            style="
                background:#555;
                color:white;
                border:none;
                padding:10px 16px;
                border-radius:6px;
                cursor:pointer;
                font-size:16px;
                display:flex;
                align-items:center;
                gap:6px;
            ">
        <i class="fas fa-arrow-left"></i> Volver a la página principal
    </button>
</form>

  <h2 style="cursor:pointer;" onclick="toggleUsuarios()"> Lista de Usuarios (click para abrir/cerrar)</h2>

<div id="listaUsuarios" style="display:none; margin-bottom:20px;">
    <table class="reporte-table">
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Estado</th>
        </tr>

        <?php
        $usuariosTodos = $pdo->query("SELECT idUser, nombre, email, rol, status FROM usuarios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuariosTodos as $u):
        ?>
        <tr>
            <td><?= htmlspecialchars($u['idUser']) ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td><?= htmlspecialchars($u['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
function toggleUsuarios() {
    const box = document.getElementById("listaUsuarios");
    box.style.display = (box.style.display === "none") ? "block" : "none";
}
</script>

  <h1>Usuarios pendientes</h1>
  <table border="1">
    <tr><th>Cédula</th><th>Nombre</th><th>Acciones</th></tr>
    <?php foreach($pending as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['idUser']) ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td>
          <a href="?action=approve&idUser=<?= $p['idUser'] ?>">Habilitar</a>
          <a href="?action=deny&idUser=<?= $p['idUser'] ?>">Denegar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h1>Horas registradas</h1>
  <table border="1">
    <tr><th>Cédula</th><th>Nombre</th><th>Fecha</th><th>Horas</th><th>Descripción</th></tr>
    <?php foreach($horas as $h): ?>
      <tr>
        <td><?= htmlspecialchars($h['idUser']) ?></td>
        <td><?= htmlspecialchars($h['nombre']) ?></td>
        <td><?= htmlspecialchars($h['work_date']) ?></td>
        <td><?= htmlspecialchars($h['horas']) ?></td>
        <td><?= htmlspecialchars($h['descripcion']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>📁 Comprobantes subidos por los usuarios</h2>
  <!-- 🔔 Notificación flotante -->
<div id="toast" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #28a745;
    color: white;
    padding: 14px 22px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.4s ease, transform 0.4s ease;
    z-index: 1000;
"></div>

<script>
function showToast(mensaje, tipo = "exito") {
    const toast = document.getElementById("toast");
    toast.textContent = mensaje;
    toast.style.background = tipo === "error" ? "#dc3545" : "#28a745";
    toast.style.opacity = "1";
    toast.style.transform = "translateY(0)";

    // Ocultar después de 2 segundos
    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateY(20px)";
    }, 2000);
}

// Detectar si vino con un mensaje desde PHP
<?php if (isset($_GET['mensaje'])): ?>
    showToast("<?= htmlspecialchars($_GET['mensaje']) ?>", "exito");
<?php elseif (isset($_GET['error'])): ?>
    showToast("<?= htmlspecialchars($_GET['error']) ?>", "error");
<?php endif; ?>
</script>


<?php
try {
    $sql = "SELECT p.*, u.nombre, u.email 
            FROM pagos p
            JOIN usuarios u ON p.id_usuario = u.idUser
            ORDER BY p.fecha_subida DESC";
    $stmt = $pdo->query($sql);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($pagos) {
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse; width:100%; text-align:center;'>";
        echo "<tr style='background-color:#ddd;'>
                <th>Usuario</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Archivo</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>";

        foreach ($pagos as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . ucfirst(htmlspecialchars($row['tipo'])) . "</td>";
            echo "<td><a href='" . htmlspecialchars($row['archivo']) . "' target='_blank'>Ver</a></td>";
            echo "<td>" . ucfirst(htmlspecialchars($row['estado'])) . "</td>";
            echo "<td>
                    <a href='validar_comprobante.php?id=" . $row['idPago'] . "&estado=aprobado'>✅ Aprobar</a> |
                    <a href='validar_comprobante.php?id=" . $row['idPago'] . "&estado=rechazado'>❌ Rechazar</a>
                  </td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No hay comprobantes subidos aún.</p>";
    }

} catch (PDOException $e) {
    echo "<p>Error al obtener los comprobantes: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
  <h2> Unidades Habitacionales</h2>

  <?php
  try {
      // Obtener usuarios y unidades
      $usuarios = $pdo->query("SELECT idUser, nombre, email FROM usuarios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
      $unidades = $pdo->query("SELECT idUnidad, numero_unidad, direccion, estado, id_usuario FROM unidades ORDER BY numero_unidad")->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      echo "<p style='color:red;'>Error al obtener datos: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>

  <table border="1">
    <tr>
      <th>Usuario</th>
      <th>Email</th>
      <th>Unidad Asignada</th>
      <th>Dirección</th>
      <th>Acción</th>
    </tr>

    <?php foreach ($usuarios as $u): 
        $unidadAsignada = null;
        foreach ($unidades as $uni) {
            if ($uni['id_usuario'] == $u['idUser']) {
                $unidadAsignada = $uni;
                break;
            }
        }
    ?>
    <tr>
      <td><?= htmlspecialchars($u['nombre']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= $unidadAsignada ? htmlspecialchars($unidadAsignada['numero_unidad']) : "<span style='color:gray;'>Sin asignar</span>" ?></td>
      <td><?= $unidadAsignada ? htmlspecialchars($unidadAsignada['direccion']) : "—" ?></td>
      <td>
        <form action="backend_asignar_unidad.php" method="POST" style="display:inline-block;">
          <input type="hidden" name="idUser" value="<?= $u['idUser'] ?>">
          <select name="idUnidad" required>
            <option value="">Seleccionar</option>
            <?php foreach ($unidades as $uni): ?>
              <option value="<?= $uni['idUnidad'] ?>" 
                <?= ($unidadAsignada && $unidadAsignada['idUnidad'] == $uni['idUnidad']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($uni['numero_unidad']) ?> (<?= $uni['estado'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit">Asignar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <h2> Crear Nueva Unidad Habitacional</h2>

<div class="reporte-container">
  <form action="backend_crear_unidad.php" method="POST">
    
    <label>Número de Unidad:</label>
    <input type="text" name="numero_unidad" required>

    <label>Dirección:</label>
    <input type="text" name="direccion" required>

    <label>Descripción:</label>
    <textarea name="descripcion" rows="3"></textarea>

    <label>Cuartos:</label>
    <input type="number" name="cuartos" min="0" required>

    <label>Baños:</label>
    <input type="number" name="banos" min="0" required>

    <label>Metros cuadrados:</label>
    <input type="number" name="metros" min="1" required>

    <label>Estado:</label>
    <select name="estado" required>
        <option value="disponible">Disponible</option>
        <option value="ocupada">Ocupada</option>
        <option value="asignada">Asignada</option>
    </select>

    <button type="submit">Crear Unidad</button>
  </form>
</div>

  <h2>Reportes de Avance de Unidades</h2>
<?php
try {
    $reportes = $pdo->query("
        SELECT r.*, u.numero_unidad, u.direccion
        FROM reportes_unidad r
        JOIN unidades u ON r.idUnidad = u.idUnidad
        ORDER BY r.fecha_reporte DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $unidadesDisponibles = $pdo->query("SELECT idUnidad, numero_unidad FROM unidades ORDER BY numero_unidad")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color:red;'>Error al obtener reportes: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
/* === Estilo general para reportes === */
.reporte-container {
  width: 90%;
  margin: 25px auto;
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

/* === Formulario === */
.reporte-container form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.reporte-container h3 {
  color: #007bff;
  margin-bottom: 10px;
}

.reporte-container label {
  font-weight: 600;
  color: #333;
}

.reporte-container select,
.reporte-container input[type="text"],
.reporte-container textarea,
.reporte-container input[type="file"] {
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
}

.reporte-container button {
  width: fit-content;
  align-self: flex-start;
  background: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.reporte-container button:hover {
  background: #0056b3;
}

/* === Tabla de reportes === */
.reporte-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 25px;
  font-size: 14px;
}

.reporte-table th {
  background: #007bff;
  color: white;
  padding: 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.reporte-table td {
  padding: 10px;
  border-bottom: 1px solid #eee;
  text-align: center;
}

.reporte-table tr:hover {
  background: #f7fbff;
}

.reporte-table a {
  color: #007bff;
  text-decoration: none;
  font-weight: 600;
}

.reporte-table a:hover {
  text-decoration: underline;
}
</style>

<div class="reporte-container">
  <form action="backend_subir_reporte.php" method="POST" enctype="multipart/form-data">
    <h3> Nuevo Reporte de Avance</h3>
    <label>Unidad:</label>
    <select name="idUnidad" required>
      <option value="">Seleccionar...</option>
      <?php foreach ($unidadesDisponibles as $u): ?>
        <option value="<?= $u['idUnidad'] ?>"><?= htmlspecialchars($u['numero_unidad']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Título:</label>
    <input type="text" name="titulo" required>

    <label>Descripción:</label>
    <textarea name="descripcion" rows="4" required></textarea>

    <label>Archivo (opcional):</label>
    <input type="file" name="archivo">

    <button type="submit">Guardar Reporte</button>
  </form>

  <table class="reporte-table">
    <tr>
      <th>Unidad</th>
      <th>Título</th>
      <th>Descripción</th>
      <th>Archivo</th>
      <th>Fecha</th>
      <th>Acción</th>
    </tr>
    <?php if ($reportes): ?>
      <?php foreach ($reportes as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['numero_unidad']) ?></td>
          <td><?= htmlspecialchars($r['titulo']) ?></td>
          <td style="max-width:300px; text-align:left;"><?= nl2br(htmlspecialchars($r['descripcion'])) ?></td>
          <td>
            <?php if ($r['archivo']): ?>
              <a href="<?= htmlspecialchars($r['archivo']) ?>" target="_blank">Ver</a>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['fecha_reporte']) ?></td>
          <td>
            <a href="backend_eliminar_reporte.php?id=<?= $r['idReporte'] ?>" onclick="return confirm('¿Eliminar este reporte?')">🗑️ Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6">No hay reportes aún.</td></tr>
    <?php endif; ?>
  </table>
</div>
<h2> Publicar nuevo aviso</h2>

<form action="crear_aviso.php" method="POST">
    <input type="text" name="titulo" placeholder="Título del aviso" required style="width:100%; padding:10px;">
    <textarea name="contenido" placeholder="Contenido del aviso" required style="width:100%; padding:10px; height:120px; margin-top:10px;"></textarea>

    <button type="submit" style="margin-top:10px; padding:12px; background:#0056b3; color:white; border:none; border-radius:6px;">
        Publicar
    </button>
</form>



</body>
</html>
