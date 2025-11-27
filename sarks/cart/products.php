<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/connection.php'; // central DB connection

// Add item to cart
if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']); // Ensure ID is an integer

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        // Fetch product details from database
        $id_safe = mysqli_real_escape_string($conn, $id);
        $sql_s = "SELECT * FROM products WHERE pdtId = $id_safe";
        $query_s = mysqli_query($conn, $sql_s);

        if ($query_s && mysqli_num_rows($query_s) > 0) {
            $row_s = mysqli_fetch_array($query_s);

            $_SESSION['cart'][$row_s['pdtId']] = array(
                "quantity" => 1,
                "price" => $row_s['price']
            );
        } else {
            $message = "This product ID is invalid!";
        }
    }
}
?>

<?php
if (isset($message)) {
    echo "<div class='alert alert-danger'>$message</div>";
}
?>

<div class="table-responsive">
    <table class="table table-dark table-hover" style="background: transparent;">
        <thead>
            <tr>
                <th scope="col" class="text-primary">Name</th>
                <th scope="col" class="text-primary">Price</th>
                <th scope="col" class="text-primary text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all products
            $sql = "SELECT * FROM products ORDER BY pdtId ASC";
            $query = mysqli_query($conn, $sql);

            if ($query) {
                while ($row = mysqli_fetch_array($query)) {
            ?>
                    <tr style="vertical-align: middle;">
                        <td class="text-white"><?php echo htmlspecialchars($row['pdtName']); ?></td>
                        <td class="text-white"><?php echo htmlspecialchars($row['price']); ?>$</td>
                        <td class="text-end">
                            <a href="index.php?page=products&action=add&id=<?php echo $row['pdtId']; ?>"
                                class="btn btn-sm btn-outline-danger">
                                <i class="bx bx-cart-add"></i> Add to Cart
                            </a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='3' class='text-center text-muted'>Error fetching products</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>