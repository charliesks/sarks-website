<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to the database
$con = mysqli_connect("sarks_mysql", "root", "root", "sarksdb");
if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Add item to cart
if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id = intval($_GET['id']); // Ensure ID is an integer

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        // Fetch product details from database
        $id_safe = mysqli_real_escape_string($con, $id);
        $sql_s = "SELECT * FROM products WHERE pdtId = $id_safe";
        $query_s = mysqli_query($con, $sql_s);

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

<h3>Product List</h3>

<?php
if (isset($message)) {
    echo "<h2 style='color: red;'>$message</h2>";
}
?>

<table style="width:100%; border-collapse: collapse; text-align: center;">
    <tr style="background-color: #f8f9fa;">
        <th style="padding: 10px;">Name</th>
        <th style="padding: 10px;">Price</th>
        <th style="padding: 10px;">Action</th>
    </tr>
    
    <?php
    // Fetch all products
    $sql = "SELECT * FROM products ORDER BY pdtId ASC";
    $query = mysqli_query($con, $sql);

    if ($query) {
        while ($row = mysqli_fetch_array($query)) {
            ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;"><?php echo htmlspecialchars($row['pdtName']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($row['price']); ?>$</td>
                <td style="padding: 10px;">
                    <a href="index.php?page=products&action=add&id=<?php echo $row['pdtId']; ?>"
                       style="font-weight: bold; color: red; text-decoration: none;">
                        Add to cart
                    </a>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='3'>Error fetching products</td></tr>";
    }
    ?>
</table>

