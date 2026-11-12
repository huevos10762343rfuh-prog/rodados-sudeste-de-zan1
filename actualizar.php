<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit("Acceso denegado");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    exit("ID de vehículo no proporcionado.");
}

$id = $_GET['id'];
$stmt = $conexion->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute([':id' => $id]);
$v = $stmt->fetch();

if (!$v) {
    exit("Vehículo no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar vehículo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form action="logicainterna.php" method="post">
    <h1>Datos a actualizar</h1>
    <input type="hidden" name="id" value="<?= htmlspecialchars($v['id']) ?>">

    <label for="marca">Marca</label>
    <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($v['marca']) ?>" required>

    <label for="modelo">Modelo</label>
    <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($v['modelo']) ?>" required>

    <label for="anio">Año</label>
    <input type="number" id="anio" name="anio" value="<?= htmlspecialchars($v['anio']) ?>" required>

    <label for="precio">Precio</label>
    <input type="number" id="precio" name="precio" value="<?= htmlspecialchars($v['precio']) ?>" required>

    <label for="descripcion">Descripción</label>
    <input type="text" id="descripcion" name="descripcion" value="<?= htmlspecialchars($v['descripcion']) ?>" required>

    <label for="imagen">Imagen (URL)</label>
    <input type="text" id="imagen" name="imagen" value="<?= htmlspecialchars($v['imagen']) ?>" required>

    <button type="submit" name="actualizar">Enviar</button>
</form>
</body>
</html>