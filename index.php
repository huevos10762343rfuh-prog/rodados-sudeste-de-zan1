<?php
session_start();
require "conexion.php";

$adminver = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
$stmt = $conexion->query("SELECT * FROM productos");
$rta = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Concesionaria Rodados Sudeste</title>
</head>
<body>
    <header>
        <?php if (!isset($_SESSION['email'])): ?>
            <div class="iniciarsesion">
                <a href="login.php"><h3>Inicio de sesión</h3></a>
            </div>
            <div>
                <a href="signin.php"><h3>Inscripción</h3></a>
            </div>
        <?php else: ?>
            <div>
                <span>Hola, <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php"><h3>Cerrar sesión</h3></a>
            </div>
        <?php endif; ?>
    </header>

    <h1>Concesionaria Rodados Sudeste</h1>
    <h2>Todos los precios están en dólares :)</h2>

    <?php if ($adminver): ?>
        <a href="nuevo.php">Añadir vehículo</a>
    <?php endif; ?>

    <div class="grid">
    <?php foreach ($rta as $cosas): ?>
        <article>
            <img src="<?= htmlspecialchars($cosas['imagen']) ?>" alt="<?= htmlspecialchars($cosas['modelo']) ?>">
            <h2><?= htmlspecialchars($cosas['marca'] . ' ' . $cosas['modelo']) ?> (<?= htmlspecialchars($cosas['anio']) ?>)</h2>
            <p><?= htmlspecialchars($cosas['descripcion']) ?></p>
            <p>$<?= htmlspecialchars($cosas['precio']) ?></p>
            <button type="button">Comprar</button>

            <?php if ($adminver): ?>
                <form action="logicainterna.php" method="post" onsubmit="return confirm('¿Eliminar este vehículo?')">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($cosas['id']) ?>">
                    <button type="submit" name="eliminar">Eliminar</button>
                </form>
                <a href="actualizar.php?id=<?= urlencode($cosas['id']) ?>">Actualizar</a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
    </div>
</body>
</html>