<?php
require 'db.php';

// Lógica para "Borrado Lógico"
if (isset($_GET['borrar'])) {
    $id = $_GET['borrar'];
    $stmt = $pdo->prepare("UPDATE clientes SET activo = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: clientes.php");
}

// Lógica de Búsqueda (Filtro por nombre o DNI)
$busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$sql = "SELECT * FROM clientes WHERE activo = 1";
if ($busqueda != '') {
    $sql .= " AND (nombre LIKE :b OR dni LIKE :b)";
}
$stmt = $pdo->prepare($sql);
if ($busqueda != '') {
    $stmt->execute(['b' => "%$busqueda%"]);
} else {
    $stmt->execute();
}
$clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
</head>
<body>
    <a href="index.php">Volver al Inicio</a>
    <h2>Ficha de Clientes</h2>

    <form method="GET">
        <input type="text" name="buscar" placeholder="Buscar por nombre o DNI..." value="<?= $busqueda ?>">
        <button type="submit">Filtrar</button>
    </nav>

    <br>
    <a href="cliente_form.php"><strong>+ Añadir Nuevo Cliente</strong></a>
    <br><br>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>DNI</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= $c['nombre'] ?></td>
                <td><?= $c['dni'] ?></td>
                <td><?= $c['telefono'] ?></td>
                <td>
                    <a href="cliente_form.php?id=<?= $c['id'] ?>">Editar</a> | 
                    <a href="clientes.php?borrar=<?= $c['id'] ?>" onclick="return confirm('¿Marcar como borrado?')">Borrar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
