<?php
require 'db.php';
$id = $_GET['id'] ?? null;
$p = ['nombre' => '', 'dni' => ''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM profesores WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE profesores SET nombre = ?, dni = ? WHERE id = ?");
        $stmt->execute([$_POST['nombre'], $_POST['dni'], $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO profesores (nombre, dni, activo) VALUES (?, ?, 1)");
        $stmt->execute([$_POST['nombre'], $_POST['dni']]);
    }
    header("Location: profesores.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Formulario Profesor</title></head>
<body>
    <h2><?= $id ? 'Editar' : 'Nuevo' ?> Profesor</h2>
    <form method="POST">
        Nombre: <input type="text" name="nombre" value="<?= $p['nombre'] ?>" required><br><br>
        DNI: <input type="text" name="dni" value="<?= $p['dni'] ?>" required><br><br>
        <button type="submit">Guardar</button>
        <a href="profesores.php">Cancelar</a>
    </form>
</body>
</html>
