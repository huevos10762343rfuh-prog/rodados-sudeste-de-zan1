<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    http_response_code(403);
    exit("Acceso denegado");
}

function agregarVehiculo($conexion) {
    $rta = $conexion->prepare("INSERT INTO productos (marca, modelo, anio, precio, descripcion, imagen)
        VALUES (:marca, :modelo, :anio, :precio, :descripcion, :imagen)");
    $rta->execute([
        ':marca' => $_POST['marca'],
        ':modelo' => $_POST['modelo'],
        ':anio' => $_POST['anio'],
        ':precio' => $_POST['precio'],
        ':descripcion' => $_POST['descripcion'],
        ':imagen' => $_POST['imagen']
    ]);
    header("Location: index.php");
    exit();
}

function actualizar($conexion) {
    $rta = $conexion->prepare("UPDATE productos SET marca = :marca, modelo = :modelo, anio = :anio,
        precio = :precio, descripcion = :descripcion, imagen = :imagen
        WHERE id = :id");
    $rta->execute([
        ':marca' => $_POST['marca'],
        ':modelo' => $_POST['modelo'],
        ':anio' => $_POST['anio'],
        ':precio' => $_POST['precio'],
        ':descripcion' => $_POST['descripcion'],
        ':imagen' => $_POST['imagen'],
        ':id' => $_POST['id']
    ]);
    header("Location: index.php");
    exit();
}

function eliminar($conexion) {
    $rta = $conexion->prepare("DELETE FROM productos WHERE id = :id");
    $rta->execute([':id' => $_POST['id']]);
    header("Location: index.php");
    exit();
}

if (isset($_POST['agregar'])) {
    agregarVehiculo($conexion);
}
if (isset($_POST['actualizar'])) {
    actualizar($conexion);
}
if (isset($_POST['eliminar'])) {
    eliminar($conexion);
}
?>