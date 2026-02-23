<?php
require 'db.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$cliente = ['nombre' => '', 'dni' => '', 'telefono' => ''];

// Si hay ID, buscamos los datos para rellenar el formulario (Modificar)
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    if (!$cliente) {
        die("Cliente no encontrado.");
    }
}

// Procesar el formulario cuando se pulsa "Guardar"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];

    if ($id) {
        // Actualizar existente
        $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, dni = ?, telefono = ? WHERE id = ?");
        $stmt->execute([$nombre, $dni, $telefono, $id]);
    } else {
        // Insertar nuevo
        $stmt = $pdo->prepare("INSERT INTO clientes (nombre, dni, telefono, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nombre, $dni, $telefono]);
    }
    header("Location: clientes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Editar' : 'Nuevo' ?> Cliente</title>
</head>
<body>
    <h2><?= $id ? 'Modificar' : 'Añadir' ?> Cliente</h2>
    <form method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required><br><br>
        
        <label>DNI:</label><br>
        <input type="text" name="dni" value="<?= htmlspecialchars($cliente['dni']) ?>" required><br><br>
        
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($cliente['telefono']) ?>"><br><br>
        
        <button type="submit">Guardar Cambios</button>
        <a href="clientes.php">Cancelar</a>
    </form>
</body>
</html>
