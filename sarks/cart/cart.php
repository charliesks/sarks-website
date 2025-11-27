<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/connection.php'; // central DB connection

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty! Redirecting to the products page.'); window.location.href='index.php';</script>";
    exit();
}

// Update cart quantities
if (isset($_POST['submit'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
        }
    }
}

// Fetch product details
$sql = "SELECT * FROM products WHERE pdtId IN (" . implode(",", array_keys($_SESSION['cart'])) . ") ORDER BY pdtId ASC";
$query = mysqli_query($conn, $sql);
$totalprice = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white m-0">Shopping Cart</h4>
    <a href="index.php" class="text-primary text-decoration-none"><i class="bx bx-arrow-back"></i> Back to Products</a>
</div>

<form method="post" action="cart.php">
    <div class="table-responsive mb-4">
        <table class="table table-dark table-hover" style="background: transparent;">
            <thead>
                <tr>
                    <th class="text-primary">Name</th>
                    <th class="text-primary">Quantity</th>
                    <th class="text-primary">Price</th>
                    <th class="text-primary">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_array($query)) {
                    $subtotal = $_SESSION['cart'][$row['pdtId']]['quantity'] * $row['price'];
                    $totalprice += $subtotal;
                ?>
                    <tr style="vertical-align: middle;">
                        <td class="text-white"><?php echo htmlspecialchars($row['pdtName']); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $row['pdtId']; ?>]"
                                class="form-control form-control-sm bg-dark text-white border-secondary"
                                style="width: 80px;"
                                value="<?php echo $_SESSION['cart'][$row['pdtId']]['quantity']; ?>" min="0" />
                        </td>
                        <td class="text-white"><?php echo $row['price']; ?>$</td>
                        <td class="text-white"><?php echo $subtotal; ?>$</td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="3" class="text-end text-white"><strong>Total Price:</strong></td>
                    <td class="text-primary"><strong><?php echo $totalprice; ?>$</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-end mb-5">
        <button type="submit" name="submit" class="btn btn-outline-danger">Update Cart</button>
    </div>
</form>

<!-- Customer Details Form -->
<div class="glass-panel p-4 mt-4">
    <h5 class="text-center mb-4 text-white">Checkout Details</h5>
    <form action="cart.php" method="post">
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label for="user" class="mb-2 text-muted">Customer Name</label>
                <input type="text" class="form-control bg-transparent text-white border-secondary" id="user" name="uname" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label for="mbl" class="mb-2 text-muted">Mobile</label>
                <input type="text" class="form-control bg-transparent text-white border-secondary" id="mbl" pattern="[0-9]{7,15}" name="umobile" required>
            </div>
        </div>
        <div class="form-group mb-4">
            <label for="adrs" class="mb-2 text-muted">Address</label>
            <input type="text" class="form-control bg-transparent text-white border-secondary" id="adrs" name="uaddress" required>
        </div>
        <div class="text-center">
            <button type="submit" name="confirm_order" class="btn-hero w-100">
                <i class="bx bx-check-circle"></i> Confirm Order
            </button>
        </div>
    </form>
</div>

<?php
// Handle order confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_order"])) {
    $cuName = $_POST["uname"];
    $cuMobile = $_POST["umobile"];
    $cuAddress = $_POST["uaddress"];

    if (!empty($cuName) && !empty($cuMobile) && !empty($cuAddress)) {
        // Re-fetch products to ensure we have data for insertion
        // Note: In a real app, you'd want to handle this more robustly
        // Here we rely on the session cart still being populated

        foreach ($_SESSION['cart'] as $id => $cartItem) {
            // We need to fetch product name and price again or store it in session
            // For simplicity, let's query the DB for each item or assume we have it.
            // Ideally, we should have fetched it above.
            // Let's do a quick fetch here to be safe.
            $p_sql = "SELECT * FROM products WHERE pdtId = $id";
            $p_query = mysqli_query($conn, $p_sql);
            $p_row = mysqli_fetch_array($p_query);

            $stmt = $conn->prepare("INSERT INTO productseorder (ordercusname, orderphone, orderaddress, pdtId, pdtName, pdtprice, pdtquantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $cuName, $cuMobile, $cuAddress, $id, $p_row['pdtName'], $p_row['price'], $cartItem['quantity']);
            $stmt->execute();
            $stmt->close();
        }

        // Clear the cart after order
        unset($_SESSION['cart']);

        echo "<script>alert('Order Confirmed!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Please fill in all the required fields.');</script>";
    }
}
?>