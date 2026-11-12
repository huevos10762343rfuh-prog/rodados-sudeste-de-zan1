<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar sesión</h1>
    <form action="auth.php" method="POST">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@mail.com" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Ingresar</button>
    </form>
    <p>¿No tienes cuenta? <a href="signin.php">Regístrate aquí</a></p>
</body>
</html>