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
    // Register user
    if (register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file)) {
        // Generate a JWT for the registered user
        $token = generate_jwt([
            'name' => $name,
            'email' => $email,
            'nick' => $nick
        ]);
        echo json_encode(['message' => 'Usuario registrado exitosamente', 'token' => $token]);
        http_response_code(201);
    } else {
        throw new Exception("Internal error while registering user.");
    }
} catch (Exception $e) {
    echo json_encode(['message' => $e->getMessage()]);
    http_response_code(500);
}
?>
