<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$barcode = $_GET['barcode'] ?? "";

if(!$barcode){

    echo json_encode([
        "error" => "Barcode missing"
    ]);
    exit;
}

$query = $conn->prepare("SELECT * FROM products WHERE barcode = :barcode");

$query->execute([
    ":barcode" => $barcode
]);

$product = $query->fetch(PDO::FETCH_ASSOC);

if(!$product){

    echo json_encode([
        "exists" => false
    ]);
    exit;
}

echo json_encode([
    "exists" => true,
    "product" => $product
]);
