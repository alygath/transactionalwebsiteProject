<?php
session_start();
include 'connection.php';

// Ensure user is logged in and is an admin
if (!isset($_SESSION['user_email']) || $_SESSION['admin'] != 1) {
    header("Location: index.php");
    exit();
}

// ✅ Handle Category Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name']);

    if (!empty($categoryName)) {
        $stmt = $connection->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $categoryName);
        if ($stmt->execute()) {
            $_SESSION['admin_success'] = "Category added successfully!";
        } else {
            $_SESSION['admin_error'] = "Failed to add category.";
        }
        $stmt->close();
    } else {
        $_SESSION['admin_error'] = "Category name cannot be empty.";
    }

    header("Location: admin.php");
    exit();
}

// ✅ Handle Product Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $productName = trim($_POST['name']);
    $productPrice = trim($_POST['price']);
    $productStock = intval($_POST['stock']);
    $productDescription = trim($_POST['description']);
    $categoryID = intval($_POST['category']); // Get selected category

    if (empty($productName) || empty($productPrice) || empty($productDescription) || $productStock < 0 || $categoryID == 0) {
        $_SESSION['admin_error'] = "All fields are required, and stock cannot be negative.";
        header("Location: admin.php");
        exit();
    }

    // Handle Image Upload
    $targetDir = "images/products/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageFileName = time() . "_" . basename($_FILES["productImage"]["name"]);
    $targetFile = $targetDir . $imageFileName;

    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFile)) {
        // Insert into products table
        $stmt = $connection->prepare("INSERT INTO products (name, price, stock, description, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdisss", $productName, $productPrice, $productStock, $productDescription, $categoryID, $imageFileName);

        if ($stmt->execute()) {
            $_SESSION['admin_success'] = "Product added successfully!";
        } else {
            $_SESSION['admin_error'] = "Failed to add product.";
        }
        $stmt->close();
    } else {
        $_SESSION['admin_error'] = "Error uploading image.";
    }

    header("Location: admin.php");
    exit();
}

// Fetch categories for dropdown
$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
?>

<?php include('header.php'); ?>

<div class="container mt-4">
    <h2>Admin - Manage Products & Categories</h2>

    <!-- ✅ Success/Error Messages -->
    <?php if (isset($_SESSION['admin_success'])): ?>
        <p class="text-success"><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_error'])): ?>
        <p class="text-danger"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></p>
    <?php endif; ?>

    <!-- ✅ Manage Categories Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3">Add New Category</h4>
            <form action="admin.php" method="post">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" name="category_name" id="category_name" class="form-control" required>
                </div>
                <button type="submit" name="add_category" class="btn btn-primary mt-3">Add Category</button>
            </form>
        </div>
    </div>

    <!-- ✅ Add Product Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-3">Add New Product</h4>
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (£):</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" name="stock" id="stock" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="">Select a Category</option>
                        <?php while ($category = $categoriesQuery->fetch_assoc()): ?>
                            <option value="<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Image:</label>
                    <input type="file" name="productImage" id="productImage" class="form-control-file" required>
                </div>
                <button type="submit" name="add_product" class="btn btn-primary mt-3">Add Product</button>
            </form>
        </div>
    </div>
	<a href="admin_console.php" class="btn btn-secondary mb-5">Back to Admin Console</a>
</div>

<?php include('footer.php'); ?>
