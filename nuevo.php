<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit("Acceso denegado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir vehículo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form action="logicainterna.php" method="post">
    <h1>Añadir vehículo</h1>

    <label for="marca">Marca</label>
    <input type="text" id="marca" name="marca" placeholder="Marca" required>

    <label for="modelo">Modelo</label>
    <input type="text" id="modelo" name="modelo" placeholder="Modelo" required>

    <label for="anio">Año</label>
    <input type="number" id="anio" name="anio" placeholder="ej: 1999" required>

    <label for="precio">Precio</label>
    <input type="number" id="precio" name="precio" placeholder="ej: 1000" required>

    <label for="descripcion">Descripción</label>
    <input type="text" id="descripcion" name="descripcion" placeholder="..." required>

    <label for="imagen">Imagen (URL)</label>
    <input type="text" id="imagen" name="imagen" placeholder="https://..." required>

    <button type="submit" name="agregar">Enviar</button>
</form>
</body>
</html>