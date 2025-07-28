<?php
session_start();
include 'connection.php';
include 'header.php';

// Get search and filter inputs
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;
$categoryID = isset($_GET['category']) ? intval($_GET['category']) : 0;
$inStock = isset($_GET['in_stock']) ? true : false;

// Fetch categories for the dropdown
$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
?>

<div class="container mt-4">
    <h2>All Products</h2>

    <!-- 🔍 Search & Filter Form -->
    <form action="products.php" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="query" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="min_price" class="form-control" placeholder="Min Price (£)" min="0" value="<?= $minPrice; ?>">
            </div>
            <div class="col-md-2">
                <input type="number" name="max_price" class="form-control" placeholder="Max Price (£)" min="0" value="<?= $maxPrice; ?>">
            </div>
            <div class="col-md-2">
                <select name="category" class="form-control">
                    <option value="0">All Categories</option>
                    <?php while ($category = $categoriesQuery->fetch_assoc()): ?>
                        <option value="<?= $category['id']; ?>" <?= ($categoryID == $category['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-1">
                <input type="checkbox" name="in_stock" id="in_stock" <?= $inStock ? 'checked' : ''; ?>>
                <label for="in_stock">In Stock</label>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <!-- Display Products -->
    <div class="row">
        <?php
        // Construct the query with filters
        $sql = "SELECT products.*, categories.name AS category_name 
                FROM products 
                LEFT JOIN categories ON products.category_id = categories.id 
                WHERE price BETWEEN ? AND ?";
        
        $params = [$minPrice, $maxPrice];
        $types = "dd";

        if (!empty($searchQuery)) {
            $sql .= " AND (products.name LIKE ? OR products.description LIKE ?)";
            $likeQuery = "%" . $searchQuery . "%";
            $params[] = $likeQuery;
            $params[] = $likeQuery;
            $types .= "ss";
        }

        if ($categoryID > 0) {
            $sql .= " AND products.category_id = ?";
            $params[] = $categoryID;
            $types .= "i";
        }

        if ($inStock) {
            $sql .= " AND products.stock > 0";
        }

        // Prepare statement
        $stmt = $connection->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="images/products/<?= htmlspecialchars($row['image']); ?>" class="card-img-top img-fluid" style="max-height: 200px; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text">£<?= number_format($row['price'], 2); ?></p>
                            <p class="card-text text-muted"><?= htmlspecialchars($row['category_name']); ?></p>
                            <p class="card-text <?= ($row['stock'] > 0) ? 'text-success' : 'text-danger'; ?>">
                                <?= ($row['stock'] > 0) ? "In Stock ({$row['stock']} available)" : "Out of Stock"; ?>
                            </p>
                            <a href="product.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Product</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-danger">No products found.</p>';
        }

        $stmt->close();
        $connection->close();
        ?>
    </div>
</div>

<?php include('footer.php'); ?>
