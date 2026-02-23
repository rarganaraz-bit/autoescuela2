<?php
$host = 'localhost';
$db   = 'autoescuela';
$user = 'root'; 
$pass = 'root'; //

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si falla con 'root', probamos con contraseña vacía automáticamente
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, '');
    } catch (PDOException $e2) {
        die("Error de conexión: " . $e2->getMessage());
    }
}
?>
