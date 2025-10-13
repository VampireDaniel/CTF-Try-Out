<?php
require_once('../includes/db.php');
session_start(); // make sure session is started

if (!isset($_SESSION['user_id'])) {
    // If not logged in, for AJAX return JSON error; else redirect
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "You must be logged in to place an order."]);
        exit();
    } else {
        header('Location: login.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity   = $_POST['quantity'] ?? 1; // quantity is optional (default to 1)
    $user_id    = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    // Validate product ID (here we assume valid product IDs are 1 through 12)
    if (!ctype_digit($product_id) || $product_id < 1 || $product_id > 12) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Invalid product ID."]);
            exit();
        } else {
            echo "Invalid product ID.";
            exit();
        }
    }

    $db = Database::getInstance();
    $pdo = $db->getPdo();

    $stmt = $pdo->prepare('
        INSERT INTO Orders (user_id, product_id, order_date)
        VALUES (:user_id, :product_id, :order_date)
    ');
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindValue(':order_date', $order_date, PDO::PARAM_STR);

    try {
        $stmt->execute();
        // For AJAX requests, return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(["success" => true, "message" => "Order placed successfully."]);
            exit();
        } else {
            header('Location: home.php');
            exit();
        }
    } catch (PDOException $e) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Failed to place order."]);
            exit();
        } else {
            echo "Failed to place order.";
            exit();
        }
    }
}
?>
