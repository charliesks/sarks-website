<?php
// products.php - Just lists products. 
// Logic for adding to cart is now handled in the central cart/index.php
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