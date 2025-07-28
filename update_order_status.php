<?php
session_start();
include 'connection.php';

// Ensure admin
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Update order status
$orderId = $_POST['order_id'];
$newStatus = $_POST['order_status'];

$stmt = $connection->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $orderId);
$stmt->execute();
$stmt->close();

header("Location: admin_orders.php");
exit();
?>
