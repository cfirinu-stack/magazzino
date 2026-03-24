<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$query = $conn->query("
    SELECT 
        movements.id,
        products.name as product_name,
        users.name as user_name,
        movements.type,
        movements.quantity,
        movements.created_at
    FROM movements
    JOIN products ON movements.product_id = products.id
    JOIN users ON movements.user_id = users.id
    ORDER BY movements.created_at DESC
    LIMIT 50
");

$movements = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($movements);
