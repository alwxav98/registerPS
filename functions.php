<?php
require_once 'config.php';

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

function register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file = null) {
    $pdo = connect_db();
    try {
        $password_hash = hash('sha256', $password);
        $image_path = null;

        if ($image_file && allowed_file($image_file['name'])) {
            $filename = basename($image_file['name']);
            $filepath = UPLOAD_FOLDER . $filename;
            move_uploaded_file($image_file['tmp_name'], $filepath);
            $image_path = $filename;
        }

        $query = "INSERT INTO Users (name, surname, nick, birthdate, Email, PasswordHash, image, CreatedAt)
                  VALUES (:name, :surname, :nick, :birthdate, :email, :password, :image, NOW())";
        $stmt = $pdo->prepare($query);
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
        error_log("Error: " . $e->getMessage());
        return false;
    }
}
?>
