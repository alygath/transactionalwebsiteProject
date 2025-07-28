<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch product stock from database
    $stmt = $connection->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();

if ($quantity > $stock) {
    $_SESSION['cart_error'] = "You cannot add more than the available stock ({$stock} remaining).";
    header("Location: product.php?id=" . $productId);
    exit();
}

    // Check if product is already in cart
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = $quantity;
    } else {
        // Ensure total quantity does not exceed stock
        if ($_SESSION['cart'][$productId] + $quantity > $stock) {
            $_SESSION['cart_error'] = "You cannot add more than the available stock.";
            header("Location: product.php?id=" . $productId);
            exit();
        }
        $_SESSION['cart'][$productId] += $quantity;
    }

    $_SESSION['cart_success'] = "Product added to cart!";
    header("Location: cart.php");
    exit();
}
?>