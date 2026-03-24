<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'] ?? "";
$type = $data['type'] ?? "";
$quantity = $data['quantity'] ?? 0;

if(!$product_id || !$type || !$quantity){

    echo json_encode([
        "error" => "Invalid data"
    ]);
    exit;
}

$query = $conn->prepare("SELECT stock_quantity FROM products WHERE id = :id");

$query->execute([
    ":id" => $product_id
]);

$product = $query->fetch(PDO::FETCH_ASSOC);

if(!$product){

    echo json_encode([
        "error" => "Product not found"
    ]);
    exit;
}

$current_stock = $product['stock_quantity'];

if($type === "IN"){
    $new_stock = $current_stock + $quantity;
}

if($type === "OUT"){
    $new_stock = $current_stock - $quantity;

    if($new_stock < 0){
        echo json_encode([
            "error" => "Not enough stock"
        ]);
        exit;
    }
}

$update = $conn->prepare("
    UPDATE products
    SET stock_quantity = :stock
    WHERE id = :id
");

$update->execute([
    ":stock" => $new_stock,
    ":id" => $product_id
]);

$movement = $conn->prepare("
    INSERT INTO movements
    (product_id,user_id,type,quantity)
    VALUES
    (:product_id,:user_id,:type,:quantity)
");

$movement->execute([
    ":product_id" => $product_id,
    ":user_id" => $_SESSION['user_id'],
    ":type" => $type,
    ":quantity" => $quantity
]);

echo json_encode([
    "success" => true,
    "new_stock" => $new_stock
]);
