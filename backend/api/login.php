<?php

session_start();

require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"));

$email = $data->email ?? "";
$password = $data->password ?? "";

if(!$email || !$password){

    echo json_encode([
        "error" => "Missing credentials"
    ]);
    exit;
}

$query = $conn->prepare("SELECT * FROM users WHERE email = :email");

$query->execute([
    ":email" => $email
]);

$user = $query->fetch(PDO::FETCH_ASSOC);

if(!$user){

    echo json_encode([
        "error" => "User not found"
    ]);
    exit;
}

if(!password_verify($password, $user['password'])){

    echo json_encode([
        "error" => "Invalid password"
    ]);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];

echo json_encode([
    "success" => true,
    "user" => $user['name']
]);
