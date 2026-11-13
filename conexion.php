
<?php

$host = '127.0.0.1';
$db = getenv("DB_NAME") ?: 'autos';
$usuario = getenv("DB_USER") ?: 'root';
$clave = getenv("DB_PASSWORD") ?: '10008796';
$puerto = 3306;

try {
    $conexion = new PDO(
        "mysql:host=$host;port=$puerto;dbname=$db;charset=utf8mb4",
        $usuario,
        $clave
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Error de conexión con la base de datos.");
}

?>
v
