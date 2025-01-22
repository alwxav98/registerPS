<?php
require_once 'config.php';
//require_once realpath(__DIR__ . '/libs/autoload.php');


require_once 'vendor/autoload.php'; // Load the JWT library
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const JWT_SECRET_KEY = "claveSegura123@"; // Change to a strong key

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
 * Register a user in the database.
 */
function register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file = null) {
    $pdo = connect_db();
    try {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $image_path = null;

        // Handling the image file
        if ($image_file && allowed_file($image_file['name'])) {
            $filename = uniqid() . "_" . basename($image_file['name']);
            $filepath = UPLOAD_FOLDER . $filename;
            move_uploaded_file($image_file['tmp_name'], $filepath);
            $image_path = $filename;
        }

        // SQL Query
        $query = "INSERT INTO Users (name, surname, nick, birthdate, Email, PasswordHash, image, CreatedAt)
                  VALUES (:name, :surname, :nick, :birthdate, :email, :password, :image, NOW())";
        $stmt = $pdo->prepare($query);

        // Execute the query with the data
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
 * Generate a JWT token.
 */
function generate_jwt($user_data) {
    $payload = [
        'iss' => "http://ec2-52-23-49-32.compute-1.amazonaws.com", 
        'aud' => "http://ec2-54-157-221-188.compute-1.amazonaws.com:5000", // Consumer (frontend or microservice)
        'iat' => time(), // Time to issue
        'nbf' => time(), // Not valid before
        'exp' => time() + 3600, // Expires in 1 hour
        'data' => $user_data // User data
    ];
    return JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
}

/**
 * Validate a JWT token.
 */
function validate_jwt($jwt) {
    try {
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));
        return (array) $decoded->data; // Returns the token data
    } catch (Exception $e) {
        error_log("JWT Error: " . $e->getMessage());
        return null; // Invalid or expired token
    }
}
?>
