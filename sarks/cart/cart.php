<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty! Redirecting to the products page.'); window.location.href='index.php';</script>";
    exit();
}

// Connect to MySQL
$conn = mysqli_connect("sarks_mysql", "root", "root", "sarksdb");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
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

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sarks - Shopping Cart</title>

    <!-- Icons -->
    <link href="assets/img/imageedit_1_2859685327.png" rel="icon">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <h4>View Cart</h4>
    <a href="index.php" style="font-weight: bold;color: red;">Go back to products page</a><br><br>

    <form method="post" action="cart.php">
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>

            <?php while ($row = mysqli_fetch_array($query)) { 
                $subtotal = $_SESSION['cart'][$row['pdtId']]['quantity'] * $row['price'];
                $totalprice += $subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['pdtName']); ?></td>
                    <td>
                        <input type="number" name="quantity[<?php echo $row['pdtId']; ?>]" size="5" value="<?php echo $_SESSION['cart'][$row['pdtId']]['quantity']; ?>" min="0"/>
                    </td>
                    <td><?php echo $row['price']; ?>$</td>
                    <td><?php echo $subtotal; ?>$</td>
                </tr>
            <?php } ?>

            <tr>
                <td colspan="3"><strong>Total Price:</strong></td>
                <td><strong><?php echo $totalprice; ?>$</strong></td>
            </tr>
        </table>

        <button type="submit" name="submit" class="btn btn-danger">Update Cart</button>
    </form>

    <br>

    <!-- Customer Details Form -->
<div class="row justify-content-center mt-4">
    <div class="col-lg-6">
        <div class="card shadow-lg p-4" style="border-radius: 10px; background: #f8f9fa;">
            <h5 class="text-center mb-4 font-weight-bold">Enter Your Details</h5>
            <form action="cart.php" method="post">
                <div class="form-group">
                    <label for="user" class="font-weight-bold">Customer Name:</label>
                    <input type="text" class="form-control" id="user" name="uname" required>
                </div>
                <div class="form-group">
                    <label for="mbl" class="font-weight-bold">Mobile:</label>
                    <input type="text" class="form-control" id="mbl" pattern="[0-9]{7,15}" name="umobile" required>
                </div>
                <div class="form-group">
                    <label for="adrs" class="font-weight-bold">Address:</label>
                    <input type="text" class="form-control" id="adrs" name="uaddress" required>
                </div>
                <button type="submit" name="confirm_order" class="btn btn-success btn-block mt-3">
                    <i class="bx bx-check-circle"></i> Confirm Order
                </button>
            </form>
        </div>
    </div>
</div>


</body>
</html>

<?php
// Handle order confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_order"])) {
    $cuName = $_POST["uname"];
    $cuMobile = $_POST["umobile"];
    $cuAddress = $_POST["uaddress"];

    if (!empty($cuName) && !empty($cuMobile) && !empty($cuAddress)) {
        foreach ($_SESSION['cart'] as $id => $cartItem) {
            $stmt = $conn->prepare("INSERT INTO productseorder (ordercusname, orderphone, orderaddress, pdtId, pdtName, pdtprice, pdtquantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissi", $cuName, $cuMobile, $cuAddress, $id, $row['pdtName'], $row['price'], $cartItem['quantity']);
            $stmt->execute();
            $stmt->close();
        }

        // Clear the cart after order
        unset($_SESSION['cart']);

        echo "<script>orderConfirmation(); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Please fill in all the required fields.');</script>";
    }
}
?>
