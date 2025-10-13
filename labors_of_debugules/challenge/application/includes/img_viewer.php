<?php
require_once('db.php');
require_once('product.php');

function getAllProductIds() {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $query = "SELECT product_id FROM Products ORDER BY product_id ASC";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getProductDetails($productId) {
    return getProductById($productId);
}

function getImageAsBase64($productId) {
    $imagePath = substr(__DIR__ . '/../public/assets/images/' . $productId . '.png', 0, 400);

    if (!file_exists($imagePath)) {
        return false;
    }

    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    return 'data:image/png;base64,' . $base64;
}
?>
