<?php
require 'db.php';
$id = $_GET['id'] ?? null;
$v = ['matricula' => '', 'modelo' => '', 'id_profesor_habitual' => ''];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM vehiculos WHERE id = ?");
    $stmt->execute([$id]);
    $v = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profe = $_POST['id_profesor_habitual'] ?: null;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE vehiculos SET matricula = ?, modelo = ?, id_profesor_habitual = ? WHERE id = ?");
        $stmt->execute([$_POST['matricula'], $_POST['modelo'], $profe, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vehiculos (matricula, modelo, id_profesor_habitual, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute([$_POST['matricula'], $_POST['modelo'], $profe]);
    }
    header("Location: vehiculos.php");
}

$profesores = $pdo->query("SELECT id, nombre FROM profesores WHERE activo = 1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Formulario Vehículo</title></head>
<body>
    <h2><?= $id ? 'Editar' : 'Nuevo' ?> Vehículo</h2>
    <form method="POST">
        Matrícula: <input type="text" name="matricula" value="<?= $v['matricula'] ?>" required><br><br>
        Modelo: <input type="text" name="modelo" value="<?= $v['modelo'] ?>" required><br><br>
        Profesor Habitual: 
        <select name="id_profesor_habitual">
            <option value="">-- Ninguno --</option>
            <?php foreach ($profesores as $pro): ?>
                <option value="<?= $pro['id'] ?>" <?= $v['id_profesor_habitual'] == $pro['id'] ? 'selected' : '' ?>>
                    <?= $pro['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Guardar</button>
        <a href="vehiculos.php">Cancelar</a>
    </form>
</body>
</html>
