<?php
session_start();
include 'connection.php';
include 'header.php';

// Get Product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$product_id) {
    header("Location: products.php"); // Redirect if no ID is provided
    exit();
}

// Fetch product details
$stmt = $connection->prepare("SELECT products.*, categories.name AS category_name FROM products 
                              LEFT JOIN categories ON products.category_id = categories.id 
                              WHERE products.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: products.php"); // Redirect if product not found
    exit();
}
$stmt->close();
?>

<div class="container mt-4">
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6">
            <img src="images/products/<?= htmlspecialchars($product['image']); ?>" 
                 class="img-fluid rounded shadow-sm" 
                 alt="<?= htmlspecialchars($product['name']); ?>" 
                 style="max-height: 400px; object-fit: contain;">
        </div>
		
		<?php if (isset($_SESSION['cart_error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['cart_error']; unset($_SESSION['cart_error']); ?>
    </div>
<?php endif; ?>

        <!-- Product Details -->
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']); ?></h2>
            <p class="text-muted"><?= htmlspecialchars($product['category_name']); ?></p>
            <h4 class="text-primary">£<?= number_format($product['price'], 2); ?></h4>
            <p><?= htmlspecialchars($product['description']); ?></p>
            <p class="<?= ($product['stock'] > 0) ? 'text-success' : 'text-danger'; ?>">
                <?= ($product['stock'] > 0) ? "In Stock ({$product['stock']} available)" : "Out of Stock"; ?>
            </p>

            <!-- Add to Cart Form -->
            <?php if ($product['stock'] > 0): ?>
            <form action="add_to_cart.php" method="POST">
    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
    <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" class="form-control w-25" min="1" max="<?= $product['stock']; ?>" value="1" required>
    </div>
    <button type="submit" class="btn btn-success mt-3">
        <i class="fa-solid fa-cart-plus"></i> Add to Cart
    </button>
</form>

            <?php else: ?>
                <button class="btn btn-secondary mt-3" disabled>Out of Stock</button>
            <?php endif; ?>

            <a href="products.php" class="btn btn-link mt-3">Back to Products</a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
