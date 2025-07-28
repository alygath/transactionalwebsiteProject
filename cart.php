<?php
session_start();
include 'connection.php';
include 'header.php';

if (isset($_SESSION['cart_success'])) {
    echo '<p class="text-success">' . $_SESSION['cart_success'] . '</p>';
    unset($_SESSION['cart_success']);
}
if (isset($_SESSION['cart_error'])) {
    echo '<p class="text-danger">' . $_SESSION['cart_error'] . '</p>';
    unset($_SESSION['cart_error']);
}

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle quantity update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $productId => $qty) {
        $stmt = $connection->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->bind_result($stock);
        $stmt->fetch();
        $stmt->close();

        if ($qty > $stock) {
            $_SESSION['cart_error'] = "You cannot set more than available stock for product ID " . $productId;
        } elseif ($qty <= 0) {
            unset($_SESSION['cart'][$productId]); // Remove item if quantity is 0
        } else {
            $_SESSION['cart'][$productId] = intval($qty);
        }
    }
}


// Handle product removal
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    unset($_SESSION['cart'][$removeId]);
    header("Location: cart.php");
    exit();
}

?>

<div class="container mt-4">
    <h2>Shopping Cart</h2>
	<?php if (isset($_SESSION['cart_error'])): ?>
    <p class="text-danger"><?= $_SESSION['cart_error']; unset($_SESSION['cart_error']); ?></p>
<?php endif; ?>

    
    <?php if (empty($_SESSION['cart'])): ?>
        <p class="text-danger">Your cart is empty.</p>
    <?php else: ?>
        <form action="cart.php" method="post">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($_SESSION['cart'] as $productId => $qty) {
                        // Fetch product details from database
                        $stmt = $connection->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->bind_param("i", $productId);
                        $stmt->execute();
                        $product = $stmt->get_result()->fetch_assoc();
                        
                        if (!$product) {
                            continue; // Skip if product not found
                        }

                        $subtotal = $product['price'] * $qty;
                        $totalPrice += $subtotal;
                    ?>
                    <tr>
                        <td><img src="images/products/<?= htmlspecialchars($product['image']); ?>" class="img-fluid" style="width: 80px; height: 80px; object-fit: contain;"></td>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td>£<?= number_format($product['price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?= $productId; ?>]" value="<?= $qty; ?>" min="1" max="<?= $product['stock']; ?>" class="form-control" style="width: 80px;">

                        </td>
                        <td>£<?= number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="cart.php?remove=<?= $productId; ?>" class="btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h4>Total: £<?= number_format($totalPrice, 2); ?></h4>
            <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
