<?php
require 'db.php';
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    // Lunes = 1, Domingo = 7. Premisa (l): Lunes a Viernes
    $dia_semana = date('N', strtotime($fecha));
    
    if ($dia_semana > 5) {
        $mensaje = "❌ Error: Solo hay clases de lunes a viernes.";
    } else {
        // Premisa (k): Cliente máximo 1 clase al día
        $stmt = $pdo->prepare("SELECT id FROM clases WHERE id_cliente = ? AND fecha = ?");
        $stmt->execute([$id_cliente, $fecha]);
        
        if ($stmt->fetch()) {
            $mensaje = "❌ Error: Este cliente ya tiene una clase este día.";
        } else {
            /* LÓGICA DE ASIGNACIÓN AUTOMÁTICA (m, n, o, p, q, r)
               Buscamos profesores que:
               1. Estén activos.
               2. No tengan clase a esa hora (o).
               3. Tengan menos de 4 horas ese día (r).
               4. Ordenamos por el que menos clases tenga para repartir carga (p).
            */
            $sql_profes = "SELECT p.*, 
                (SELECT COUNT(*) FROM clases WHERE id_profesor = p.id AND fecha = :f) as horas_hoy
                FROM profesores p 
                WHERE p.activo = 1 
                AND NOT EXISTS (SELECT 1 FROM clases WHERE id_profesor = p.id AND fecha = :f AND hora = :h)
                HAVING horas_hoy < 4
                ORDER BY horas_hoy ASC";
            
            $stmtP = $pdo->prepare($sql_profes);
            $stmtP->execute(['f' => $fecha, 'h' => $hora]);
            $profesores_aptos = $stmtP->fetchAll();

            $asignado = false;
            foreach ($profesores_aptos as $profe) {
                /*
                  Para cada profesor apto, buscamos un vehículo que:
                  1. Esté libre a esa hora (n).
                  2. Priorizamos el vehículo donde este profe sea el habitual (q).
                */
                $sql_vehiculo = "SELECT v.* FROM vehiculos v 
                    WHERE v.activo = 1 
                    AND NOT EXISTS (SELECT 1 FROM clases WHERE id_vehiculo = v.id AND fecha = :f AND hora = :h)
                    ORDER BY (v.id_profesor_habitual = :pid) DESC LIMIT 1";
                
                $stmtV = $pdo->prepare($sql_vehiculo);
                $stmtV->execute(['f' => $fecha, 'h' => $hora, 'pid' => $profe['id']]);
                $vehiculo = $stmtV->fetch();

                if ($vehiculo) {
                    // Si encontramos pareja de Profe + Vehículo, guardamos
                    $ins = $pdo->prepare("INSERT INTO clases (id_cliente, id_profesor, id_vehiculo, fecha, hora) VALUES (?, ?, ?, ?, ?)");
                    $ins->execute([$id_cliente, $profe['id'], $vehiculo['id'], $fecha, $hora]);
                    $mensaje = "✅ Clase asignada: Profe " . $profe['nombre'] . " con el coche " . $vehiculo['matricula'];
                    $asignado = true;
                    break; 
                }
            }

            if (!$asignado) {
                $mensaje = "❌ Error: No hay recursos disponibles (profe o coche) para esa hora.";
            }
        }
    }
}

// Datos para la vista
$clientes = $pdo->query("SELECT id, nombre FROM clientes WHERE activo = 1 ORDER BY nombre ASC")->fetchAll();
$agenda = $pdo->query("SELECT c.*, cl.nombre as cliente, p.nombre as profe, v.matricula 
                       FROM clases c 
                       JOIN clientes cl ON c.id_cliente = cl.id 
                       JOIN profesores p ON c.id_profesor = p.id 
                       JOIN vehiculos v ON c.id_vehiculo = v.id 
                       ORDER BY c.fecha DESC, c.hora ASC LIMIT 20")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Clases</title>
    <style>body{font-family:sans-serif; margin:30px;} .msg{font-weight:bold; color:blue;}</style>
</head>
<body>
    <a href="index.php">⬅ Volver al Menú</a>
    <h1>📅 Agenda de Clases</h1>
    
    <?php if($mensaje): ?> <p class="msg"><?= $mensaje ?></p> <?php endif; ?>

    <form method="POST" style="background:#f4f4f4; padding:20px; border-radius:10px;">
        <h3>Nueva Reserva (Asignación Automática)</h3>
        Cliente: 
        <select name="id_cliente" required>
            <?php foreach($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
        
        Fecha: <input type="date" name="fecha" required value="<?= date('Y-m-d') ?>">
        
        Hora: 
        <select name="hora">
            <?php for($i=10; $i<=17; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?>:00h</option>
            <?php endfor; ?>
        </select>
        
        <button type="submit">Reservar Clase</button>
    </form>

    <hr>
    <h3>Próximas Clases</h3>
    <table border="1" width="100%" cellpadding="10" style="border-collapse:collapse;">
        <tr style="background:#eee;">
            <th>Fecha</th><th>Hora</th><th>Cliente</th><th>Profesor</th><th>Vehículo</th>
        </tr>
        <?php foreach($agenda as $clase): ?>
        <tr>
            <td><?= $clase['fecha'] ?></td>
            <td><?= $clase['hora'] ?>:00h</td>
            <td><?= $clase['cliente'] ?></td>
            <td><?= $clase['profe'] ?></td>
            <td><?= $clase['matricula'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
