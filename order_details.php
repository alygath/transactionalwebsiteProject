<?php
session_start();
include 'connection.php';
include 'header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch order details
$orderStmt = $connection->prepare("SELECT * FROM orders WHERE id = ? AND user_email = ?");
$orderStmt->bind_param("is", $orderId, $userEmail);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

// Fetch order items
$itemStmt = $connection->prepare("SELECT order_items.*, products.name, products.image 
                                  FROM order_items 
                                  JOIN products ON order_items.product_id = products.id 
                                  WHERE order_items.order_id = ?");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$items = $itemStmt->get_result();
$itemStmt->close();

if (!$order) {
    header("Location: account.php");
    exit();
}
?>

<div class="container mt-4">
    <h2>Order Details - #<?= $orderId; ?></h2>
    <p><strong>Order Date:</strong> <?= $order['order_date']; ?></p>
    <p><strong>Total:</strong> £<?= number_format($order['total_price'], 2); ?></p>
	<p><strong>Status:</strong> <?= htmlspecialchars($order['order_status']); ?></p> <!-- ✅ Show order status -->

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
					
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><img src="images/products/<?= htmlspecialchars($item['image']); ?>" class="img-fluid" style="width: 80px; height: 80px; object-fit: contain;"></td>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td><?= $item['quantity']; ?></td>
                        <td>£<?= number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
