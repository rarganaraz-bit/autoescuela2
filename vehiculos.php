<?php
require 'db.php';

if (isset($_GET['borrar'])) {
    $stmt = $pdo->prepare("UPDATE vehiculos SET activo = 0 WHERE id = ?");
    $stmt->execute([$_GET['borrar']]);
    header("Location: vehiculos.php");
}

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
// Hacemos un JOIN para ver el nombre del profesor habitual
$sql = "SELECT v.*, p.nombre as profe FROM vehiculos v 
        LEFT JOIN profesores p ON v.id_profesor_habitual = p.id 
        WHERE v.activo = 1";
if ($busqueda != '') { $sql .= " AND (v.matricula LIKE :b OR v.modelo LIKE :b)"; }

$stmt = $pdo->prepare($sql);
$busqueda != '' ? $stmt->execute(['b' => "%$busqueda%"]) : $stmt->execute();
$vehiculos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Vehículos</title></head>
<body>
    <a href="index.php">Volver</a>
    <h2>Flota de Vehículos</h2>
    <form method="GET">
        <input type="text" name="buscar" placeholder="Matrícula o Modelo..." value="<?= $busqueda ?>">
        <button type="submit">Buscar</button>
    </form>
    <br><a href="vehiculo_form.php"><strong>+ Nuevo Vehículo</strong></a><br><br>
    <table border="1">
        <tr><th>Matrícula</th><th>Modelo</th><th>Profesor Habitual</th><th>Acciones</th></tr>
        <?php foreach ($vehiculos as $v): ?>
        <tr>
            <td><?= $v['matricula'] ?></td>
            <td><?= $v['modelo'] ?></td>
            <td><?= $v['profe'] ?? 'No asignado' ?></td>
            <td>
                <a href="vehiculo_form.php?id=<?= $v['id'] ?>">Editar</a> | 
                <a href="vehiculos.php?borrar=<?= $v['id'] ?>" onclick="return confirm('¿Borrar?')">Borrar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
