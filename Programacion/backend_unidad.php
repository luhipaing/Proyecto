<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('<p style="color:red;">Acceso denegado</p>');
}

try {
    // Consultar la unidad del usuario con todos los datos nuevos
    $stmt = $pdo->prepare("
        SELECT 
            numero_unidad,
            direccion,
            
            descripcion,
            cuartos,
            banos,
            metros,
            estado,
            fecha_asignacion
        FROM unidades 
        WHERE id_usuario = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $unidad = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($unidad) {
        echo "<div class='unidad-card'>";
        echo "<h3>🏠 Unidad Habitacional Nº " . htmlspecialchars($unidad['numero_unidad']) . "</h3>";

        echo "<p><strong>Dirección:</strong> " . htmlspecialchars($unidad['direccion']) . "</p>";

        echo "<p><strong>Descripción:</strong><br>" . nl2br(htmlspecialchars($unidad['descripcion'])) . "</p>";

       

        echo "<p><strong>Cuartos:</strong> " . htmlspecialchars($unidad['cuartos']) . "</p>";

        echo "<p><strong>Baños:</strong> " . htmlspecialchars($unidad['banos']) . "</p>";

        echo "<p><strong>Metros cuadrados:</strong> " . htmlspecialchars($unidad['metros']) . " m²</p>";

        echo "<p><strong>Estado:</strong> " . ucfirst(htmlspecialchars($unidad['estado'])) . "</p>";

        echo "<p><strong>Asignada el:</strong> " . htmlspecialchars($unidad['fecha_asignacion']) . "</p>";

        echo "</div>";
    } else {
        echo "<p style='text-align:center; color:gray;'>Aún no tienes una unidad asignada.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error al obtener información: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr><h3 style='margin-top:20px;'>📊 Reportes de avance</h3>";

try {
    $stmt = $pdo->prepare("
        SELECT titulo, descripcion, fecha_reporte, archivo
        FROM reportes_unidad
        WHERE idUnidad = (SELECT idUnidad FROM unidades WHERE id_usuario = ?)
        ORDER BY fecha_reporte DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($reportes) {
        foreach ($reportes as $r) {
            echo "<div class='reporte-card'>";
            echo "<h4>📄 " . htmlspecialchars($r['titulo']) . "</h4>";
            echo "<p>" . nl2br(htmlspecialchars($r['descripcion'])) . "</p>";
            echo "<small><em>" . htmlspecialchars($r['fecha_reporte']) . "</em></small><br>";

            if ($r['archivo']) {
                echo "<a href='" . htmlspecialchars($r['archivo']) . "' target='_blank'>Ver archivo adjunto</a>";
            }

            echo "</div>";
        }
    } else {
        echo "<p style='color:gray;'>Aún no hay reportes para tu unidad.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error al cargar reportes: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
