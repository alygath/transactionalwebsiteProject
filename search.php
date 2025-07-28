<?php
session_start();
include 'connection.php';
include 'header.php';

// Get the search query
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';

?>
<div class="container mt-4">
    <h2>Search Results for: "<?= htmlspecialchars($searchQuery); ?>"</h2>

    <?php
    if (!empty($searchQuery)) {
        // Search products by name or description
        $stmt = $connection->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $likeQuery = "%" . $searchQuery . "%";
        $stmt->bind_param("ss", $likeQuery, $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="row">';
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="images/products/<?= htmlspecialchars($row['image']); ?>" class="card-img-top img-fluid" style="max-height: 200px; object-fit: contain;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text">£<?= number_format($row['price'], 2); ?></p>
                            <a href="product.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Product</a>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p class="text-danger">No products found matching your search.</p>';
        }

        $stmt->close();
    } else {
        echo '<p class="text-danger">Please enter a search term.</p>';
    }

    $connection->close();
    ?>
</div>
<?php include('footer.php'); ?>
