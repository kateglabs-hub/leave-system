<?php
require_once __DIR__ . '/../classes/Auth.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

$auth = new Auth();
$result = $auth->login($email, $password);

echo json_encode($result);
