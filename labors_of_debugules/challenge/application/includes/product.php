<?php
require_once('db.php');

function getProductById($productId) {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $query = "SELECT * FROM Products WHERE product_id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProducts() {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    $query = "SELECT p.*, COUNT(o.order_id) as order_count 
              FROM Products p 
              LEFT JOIN Orders o ON p.product_id = o.product_id 
              GROUP BY p.product_id";

    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserProducts($userId) {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $query = "SELECT p.*, COUNT(o.order_id) as order_count 
              FROM Products p 
              LEFT JOIN Orders o ON p.product_id = o.product_id 
              WHERE p.admin_only = 0 
                 OR (p.admin_only = 1 
                     AND (SELECT role FROM Users WHERE user_id = :user_id) = 'administrator')
              GROUP BY p.product_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRecentOrders() {
    $db = Database::getInstance();
    $pdo = $db->getPdo();

    //  For statistics purposes ;)

    $query = "SELECT u.username as name, p.name as product_name, p.price, o.order_date
              FROM Orders o
              JOIN Users u ON o.user_id = u.user_id
              JOIN Products p ON o.product_id = p.product_id
              ORDER BY o.order_date DESC
              LIMIT 10";

    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
