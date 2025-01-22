<?php
require_once '../functions.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

// Get data from request body
$data = $_POST ?: json_decode(file_get_contents('php://input'), true);
$name = htmlspecialchars($data['name'] ?? null, ENT_QUOTES);
$surname = htmlspecialchars($data['surname'] ?? null, ENT_QUOTES);
$nick = htmlspecialchars($data['nick'] ?? null, ENT_QUOTES);
$birthdate = $data['birthdate'] ?? null;
$email = htmlspecialchars($data['email'] ?? null, ENT_QUOTES);
$password = $data['password'] ?? null;
$image_file = $_FILES['image'] ?? null;

// Validate required fields
if (!$name || !$surname || !$nick || !$birthdate || !$email || !$password) {
    echo json_encode(['message' => 'All fields are required']);
    http_response_code(400);
    exit;
}

try {
    error_log("Iniciando registro del usuario: $email");

    if (register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file)) {
        error_log("Usuario registrado correctamente: $email");

        $token = generate_jwt([
            'name' => $name,
            'email' => $email,
            'nick' => $nick
        ]);

        if ($token) {
            error_log("Token generado correctamente: $token");
        } else {
            error_log("Error: No se pudo generar el token.");
        }

        echo json_encode([
            'message' => 'Usuario registrado exitosamente',
            'token' => $token
        ]);
        http_response_code(201);
    } else {
        throw new Exception("Error interno al registrar usuario.");
    }
} catch (Exception $e) {
    error_log("Error en el registro: " . $e->getMessage());
    echo json_encode(['message' => $e->getMessage()]);
    http_response_code(500);
}

?>
