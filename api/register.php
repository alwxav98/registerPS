<?php
require_once '../functions.php';

header('Content-Type: application/json');

$data = $_POST ?: json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? null;
$surname = $data['surname'] ?? null;
$nick = $data['nick'] ?? null;
$birthdate = $data['birthdate'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$image_file = $_FILES['image'] ?? null;

if (!$name || !$surname || !$nick || !$birthdate || !$email || !$password) {
    echo json_encode(['message' => 'Todos los campos son obligatorios']);
    http_response_code(400);
    exit;
}

if (register_user($name, $surname, $nick, $birthdate, $email, $password, $image_file)) {
    echo json_encode(['message' => 'Usuario registrado exitosamente']);
    http_response_code(201);
} else {
    echo json_encode(['message' => 'Error al registrar el usuario']);
    http_response_code(500);
}
?>
