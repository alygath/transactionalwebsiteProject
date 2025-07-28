<?php
session_start();
include 'connection.php';

// Ensure cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$orderTotal = 0;
$orderStatus = "Processing"; // Default status

// ✅ Add error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Check database connection
if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

// ✅ Prepare SQL statement and check for errors
$orderStmt = $connection->prepare("INSERT INTO orders (user_email, total_price, order_status, order_date) VALUES (?, ?, ?, NOW())");
if (!$orderStmt) {
    die("Error preparing statement: " . $connection->error); // Debugging output
}

$orderStmt->bind_param("sds", $userEmail, $orderTotal, $orderStatus);
$orderStmt->execute();
$orderId = $orderStmt->insert_id;
$orderStmt->close();

// ✅ Process each product in the cart
foreach ($_SESSION['cart'] as $productId => $qty) {
    $stmt = $connection->prepare("SELECT price, stock FROM products WHERE id = ?");
    if (!$stmt) {
        die("Error preparing product query: " . $connection->error); // Debugging output
    }

    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($productPrice, $stock);
    $stmt->fetch();
    $stmt->close();

    if ($qty > $stock) {
        $_SESSION['cart'][$productId] = $stock;
        $_SESSION['order_error'] = "Stock updated. Only {$stock} available.";
        header("Location: cart.php");
        exit();
    }

    $subtotal = $productPrice * $qty;
    $orderTotal += $subtotal;

    // ✅ Insert into `order_items`
    $orderItemStmt = $connection->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    if (!$orderItemStmt) {
        die("Error preparing order item query: " . $connection->error);
    }

    $orderItemStmt->bind_param("iiid", $orderId, $productId, $qty, $productPrice);
    $orderItemStmt->execute();
    $orderItemStmt->close();

    // ✅ Reduce stock
    $stockStmt = $connection->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    if (!$stockStmt) {
        die("Error preparing stock update query: " . $connection->error);
    }

    $stockStmt->bind_param("ii", $qty, $productId);
    $stockStmt->execute();
    $stockStmt->close();
}

// ✅ Update order total
$updateOrderStmt = $connection->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
if (!$updateOrderStmt) {
    die("Error preparing order total update: " . $connection->error);
}

$updateOrderStmt->bind_param("di", $orderTotal, $orderId);
$updateOrderStmt->execute();
$updateOrderStmt->close();

// ✅ Clear cart
unset($_SESSION['cart']);
$_SESSION['payment_status'] = "success";
$_SESSION['payment_message'] = "Your order has been placed successfully!";

// Redirect to order confirmation page
header("Location: mock_payment_success.php");
exit();
?>
