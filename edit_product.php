<?php
session_start();
include 'connection.php';
include 'header.php';

// Ensure user is admin
if (!isset($_SESSION['user_email']) || $_SESSION['admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product data
$stmt = $connection->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    $_SESSION['admin_error'] = "Product not found.";
    header("Location: manage_products.php");
    exit();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $stmt = $connection->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $name, $description, $price, $stock, $product_id);

    if ($stmt->execute()) {
        $_SESSION['admin_success'] = "Product updated successfully!";
    } else {
        $_SESSION['admin_error'] = "Failed to update product.";
    }
    $stmt->close();

    // Handle image upload
    if (!empty($_FILES['productImage']['name'])) {
        $targetDir = "images/products/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = basename($_FILES["productImage"]["name"]);
        $targetFile = $targetDir . $imageName;

        // Optional: validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['productImage']['type'], $allowedTypes)) {
            $_SESSION['admin_error'] = "Invalid file type.";
            header("Location: edit_product.php?id=" . $product_id);
            exit();
        }

        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
            $stmt = $connection->prepare("UPDATE products SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $imageName, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['admin_error'] = "Error uploading new image.";
        }
    }

    header("Location: manage_products.php");
    exit();
}
?>

<div class="container mt-4">
    <h2>Edit Product</h2>

    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form action="edit_product.php?id=<?= $product_id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Price (£):</label>
                    <input type="number" name="price" step="0.01" class="form-control" value="<?= $product['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
                </div>

                <!-- ✅ New: Upload New Image Field -->
                <div class="form-group mt-3">
                    <label for="productImage">Upload New Product Image:</label>
                    <input type="file" name="productImage" id="productImage" class="form-control-file">
                </div>

                <button type="submit" name="update_product" class="btn btn-primary mt-3">Update Product</button>
            </form>
        </div>

        <div class="col-md-4 mb-5">
            <div class="card">
                <img src="images/products/<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Current Image">
                <div class="card-body">
                    <p class="card-text text-center">Current Product Image</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
