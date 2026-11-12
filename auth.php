<?php
session_start();
require "conexion.php";

$email = $_POST['email'] ?? '';
$contrasena = $_POST['password'] ?? '';

if (!$email || !$contrasena) {
    exit("Todos los campos deben llenarse");
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($contrasena, $usuario['password'])) {
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['rol'] = $usuario['rol'] ?? 'usuario';
    header('Location: index.php');
    exit;
}

echo "Email o contraseña incorrectos";
?>