<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="/static/styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Usuario</h1>
        <form action="/api/register.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nombre" required>
            <input type="text" name="surname" placeholder="Apellido" required>
            <input type="text" name="nick" placeholder="Usuario" required>
            <input type="date" name="birthdate" required>
            <input type="email" name="email" placeholder="Correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="file" name="image">
            <button type="submit" class="btn">Registrar</button>
        </form>
        </form>
        <button class="btn btn-secondary" onclick="window.location.href='/'">Volver al Inicio</button>

        <div class="form-footer">
            <p>¿Ya tienes una cuenta? <a href="http://ec2-54-157-221-188.compute-1.amazonaws.com:5000/">Inicia sesión aquí</a></p>
        </div>

    </div>
</body>
</html>
