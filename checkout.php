<?php
session_start();
include 'header.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php"); // Redirect if cart is empty
    exit();

include 'connection.php';

// Require login
if (!isset($_SESSION['user_email'])) {
    $_SESSION['error_message'] = "You must be logged in to proceed to checkout.";
    header("Location: login.php");
    exit();
}
}
?>




<div class="container mt-5">
    <h2>Checkout</h2>
    
    <h4>Billing Information</h4>
    <form action="mock_payment.php" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address">Shipping Address:</label>
            <input type="text" name="address" id="address" class="form-control" required>
        </div>

        <h4 class="mt-4">Order Summary</h4>
        <ul class="list-group mb-3">
            <?php
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $stmt = $connection->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();

                if (!$product) continue;

                $subtotal = $product['price'] * $qty;
                $totalPrice += $subtotal;
                ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($product['name']); ?> (x<?= $qty; ?>)</span>
                    <strong>£<?= number_format($subtotal, 2); ?></strong>
                </li>
            <?php } ?>
        </ul>

        <h4>Total: £<?= number_format($totalPrice, 2); ?></h4>

        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
    </form>
</div>

<?php include 'footer.php'; ?>
