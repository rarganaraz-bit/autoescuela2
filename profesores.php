<?php
require 'db.php';

if (isset($_GET['borrar'])) {
    $stmt = $pdo->prepare("UPDATE profesores SET activo = 0 WHERE id = ?");
    $stmt->execute([$_GET['borrar']]);
    header("Location: profesores.php");
}

$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$sql = "SELECT * FROM profesores WHERE activo = 1";
if ($busqueda != '') { $sql .= " AND (nombre LIKE :b OR dni LIKE :b)"; }
$stmt = $pdo->prepare($sql);
$busqueda != '' ? $stmt->execute(['b' => "%$busqueda%"]) : $stmt->execute();
$profesores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Profesores</title></head>
<body>
    <a href="index.php">Volver</a>
    <h2>Gestión de Profesores</h2>
    <form method="GET">
        <input type="text" name="buscar" placeholder="Nombre o DNI..." value="<?= $busqueda ?>">
        <button type="submit">Buscar</button>
    </form>
    <br><a href="profesor_form.php"><strong>+ Nuevo Profesor</strong></a><br><br>
    <table border="1">
        <tr><th>ID</th><th>Nombre</th><th>DNI</th><th>Acciones</th></tr>
        <?php foreach ($profesores as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['nombre'] ?></td>
            <td><?= $p['dni'] ?></td>
            <td>
                <a href="profesor_form.php?id=<?= $p['id'] ?>">Editar</a> | 
                <a href="profesores.php?borrar=<?= $p['id'] ?>" onclick="return confirm('¿Borrar?')">Borrar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
