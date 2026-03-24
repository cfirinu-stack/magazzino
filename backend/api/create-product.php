<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$data = json_decode(file_get_contents("php://input"), true);

$barcode = $data['barcode'] ?? "";
$name = $data['name'] ?? "";
$description = $data['description'] ?? "";
$shelf = $data['shelf_location'] ?? "";
$quantity = $data['quantity'] ?? 0;

if(!$barcode || !$name){

    echo json_encode([
        "error" => "Missing data"
    ]);
    exit;
}

$query = $conn->prepare("
    INSERT INTO products
    (barcode, name, description, shelf_location, stock_quantity)
    VALUES
    (:barcode,:name,:description,:shelf,:qty)
");

$query->execute([
    ":barcode" => $barcode,
    ":name" => $name,
    ":description" => $description,
    ":shelf" => $shelf,
    ":qty" => $quantity
]);

$product_id = $conn->lastInsertId();

$movement = $conn->prepare("
    INSERT INTO movements
    (product_id,user_id,type,quantity)
    VALUES
    (:product_id,:user_id,'IN',:qty)
");

$movement->execute([
    ":product_id" => $product_id,
    ":user_id" => $_SESSION['user_id'],
    ":qty" => $quantity
]);

echo json_encode([
    "success" => true,
    "product_id" => $product_id
]);
