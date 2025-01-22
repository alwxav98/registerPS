<?php
require_once 'config.php';
//require_once realpath(__DIR__ . '/libs/autoload.php');


require_once 'vendor/autoload.php'; // Cargar la librería JWT
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const JWT_SECRET_KEY = "claveSegura123@"; // Cambiar por una clave fuerte

function connect_db() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

function allowed_file($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array(strtolower($ext), ALLOWED_EXTENSIONS);
}

/**
 * Registrar un usuario en la base de datos.
 */
function register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file = null) {
    $pdo = connect_db();
    try {
        // Hashear la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $image_path = null;

        // Manejar el archivo de imagen
        if ($image_file && allowed_file($image_file['name'])) {
            $filename = uniqid() . "_" . basename($image_file['name']);
            $filepath = UPLOAD_FOLDER . $filename;
            move_uploaded_file($image_file['tmp_name'], $filepath);
            $image_path = $filename;
        }

        // Consulta SQL
        $query = "INSERT INTO Users (name, surname, nick, birthdate, Email, PasswordHash, image, CreatedAt)
                  VALUES (:name, :surname, :nick, :birthdate, :email, :password, :image, NOW())";
        $stmt = $pdo->prepare($query);

        // Ejecutar la consulta con los datos
        $stmt->execute([
            ':name' => $name,
            ':surname' => $surname,
            ':nick' => $nick,
            ':birthdate' => $birthdate,
            ':email' => $email,
            ':password' => $password_hash,
            ':image' => $image_path
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}


/**
 * Generar un token JWT.
 */
function generate_jwt($user_data) {
    $payload = [
        'iss' => "http://localhost", // Cambiar por la IP de tu instancia EC2
        'aud' => "http://ec2-54-157-221-188.compute-1.amazonaws.com:5000", // Cambiar por el consumidor (frontend o microservicio)
        'iat' => time(), // Tiempo de emisión
        'nbf' => time(), // No válido antes de
        'exp' => time() + 3600, // Expira en 1 hora
        'data' => $user_data // Datos del usuario
    ];
    return JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
}

/**
 * Validar un token JWT.
 */
function validate_jwt($jwt) {
    try {
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        return (array) $decoded->data; // Retorna los datos del token
    } catch (Exception $e) {
        error_log("JWT Error: " . $e->getMessage());
        return null; // Token inválido o expirado
    }
}
?>
