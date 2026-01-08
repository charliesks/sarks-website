<?php
require_once __DIR__ . '/../includes/session_ready.php';
require_once __DIR__ . '/../includes/connection.php'; // central DB connection

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    session_write_close();
    echo "<script>alert('Your cart is empty! Redirecting to the products page.'); window.location.href='index.php';</script>";
    exit();
}

// Update cart quantities
if (isset($_POST['submit'])) {
    foreach ($_POST['quantity'] as $id => $quantity) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $quantity;
        }
    }
}

// Check if cart is empty again after updates
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is now empty!'); window.location.href='index.php';</script>";
    exit();
}

// Fetch product details
$product_ids = implode(",", array_keys($_SESSION['cart']));
$sql = "SELECT * FROM products WHERE pdtId IN ($product_ids) ORDER BY pdtId ASC";
$query = mysqli_query($conn, $sql);
$totalprice = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white m-0">Shopping Cart</h4>
    <a href="index.php" class="text-primary text-decoration-none"><i class="bx bx-arrow-back"></i> Back to Products</a>
</div>

<form method="post" action="index.php?page=cart">
    <div class="table-responsive mb-4">
        <table class="table table-dark table-hover" style="background: transparent;">
            <thead>
                <tr>
                    <th class="text-primary">Name</th>
                    <th class="text-primary">Quantity</th>
                    <th class="text-primary">Price</th>
                    <th class="text-primary">Subtotal</th>
                    <th class="text-primary">Action</th>
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
                            <div class="input-group input-group-sm" style="width: 130px;">
                                <a href="index.php?page=cart&action=decrease&id=<?php echo $row['pdtId']; ?>" class="btn btn-outline-secondary border-secondary text-white">-</a>
                                <input type="text" readonly class="form-control bg-dark text-white border-secondary text-center" 
                                    value="<?php echo $_SESSION['cart'][$row['pdtId']]['quantity']; ?>">
                                <a href="index.php?page=cart&action=increase&id=<?php echo $row['pdtId']; ?>" class="btn btn-outline-secondary border-secondary text-white">+</a>
                            </div>
                        </td>
                        <td class="text-white"><?php echo $row['price']; ?>$</td>
                        <td class="text-white"><?php echo $subtotal; ?>$</td>
                        <td>
                            <a href="index.php?page=cart&action=remove&id=<?php echo $row['pdtId']; ?>" class="btn btn-sm btn-outline-danger" title="Remove from cart">
                                <i class="bx bx-trash"></i>
                            </a>
                        </td>
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
    <form action="index.php?page=cart" method="post">
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label for="user" class="mb-2 text-muted">Customer Name</label>
                <input type="text" class="form-control bg-transparent text-white border-secondary" id="user" name="uname" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label for="mbl" class="mb-2 text-muted">Mobile</label>
                <input type="text" class="form-control bg-transparent text-white border-secondary" id="mbl" pattern="[0-9]{7,15}" name="umobile" required>
            </div>
            <div class="col-md-6 form-group mb-3">
                <label for="email" class="mb-2 text-muted">Email</label>
                <input type="email" class="form-control bg-transparent text-white border-secondary" id="email" name="uemail" value="<?php echo $_SESSION['cuEmail'] ?? ''; ?>" required>
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
    $cuEmail = $_POST["uemail"];
    $cuAddress = $_POST["uaddress"];

    if (!empty($cuName) && !empty($cuMobile) && !empty($cuEmail) && !empty($cuAddress)) {
        require_once __DIR__ . '/../includes/config.php';
        require_once __DIR__ . '/../includes/stripe_handler.php';

        $order_group_id = uniqid('ord_');
        $total_price = 0;
        $line_items = [];
        $order_items = [];

        // 1. Prepare Order Data
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $id => $cartItem) {
                $p_sql = "SELECT * FROM products WHERE pdtId = $id";
                $p_query = mysqli_query($conn, $p_sql);
                if ($p_row = mysqli_fetch_array($p_query)) {
                    $item_total = $p_row['price'] * $cartItem['quantity'];
                    $total_price += $item_total;

                    $order_items[] = [
                        'pdtId' => $id,
                        'quantity' => $cartItem['quantity'],
                        'price' => $p_row['price'],
                        'total' => $item_total,
                        'name' => $p_row['pdtName']
                    ];

                    if ($p_row['price'] > 0) {
                        $line_items[] = [
                            'price_data' => [
                                'currency' => 'usd',
                                'product_data' => [
                                    'name' => $p_row['pdtName'],
                                ],
                                'unit_amount' => $p_row['price'] * 100, // Stripe expects cents
                            ],
                            'quantity' => $cartItem['quantity'],
                        ];
                    }
                }
            }
        }

        // 2. Insert Pending Order into Database
        foreach ($order_items as $item) {
            $stmt = $conn->prepare("INSERT INTO productsorder (pdtId, pdtquantity, pdtprice, totalprice, ordercusname, orderphone, orderemail, orderaddress, payment_status, order_group_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
            $stmt->bind_param("iiddsssss", $item['pdtId'], $item['quantity'], $item['price'], $item['total'], $cuName, $cuMobile, $cuEmail, $cuAddress, $order_group_id);
            $stmt->execute();
            $stmt->close();
        }

        // 3. Handle Payment or Immediate Confirmation
        if ($total_price > 0) {
            try {
                $stripe = new StripeHandler(STRIPE_SECRET_KEY);
                $session = $stripe->createCheckoutSession([
                    'payment_method_types' => ['card'],
                    'line_items' => $line_items,
                    'mode' => 'payment',
                    'success_url' => BASE_URL . '/cart/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => BASE_URL . '/cart/payment_cancel.php',
                    'client_reference_id' => $order_group_id,
                    'customer_email' => $cuEmail,
                ]);

                // Update orders with Stripe Session ID
                $stripe_session_id = $session['id'];
                $update_stmt = $conn->prepare("UPDATE productsorder SET stripe_session_id = ? WHERE order_group_id = ?");
                $update_stmt->bind_param("ss", $stripe_session_id, $order_group_id);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect to Stripe
                header("Location: " . $session['url']);
                exit();
            } catch (Exception $e) {
                error_log("Stripe Session Creation Failed: " . $e->getMessage());
                echo "<script>alert('There was an error connecting to the payment provider. Please try again.');</script>";
            }
        } else {
            // Free Order (0$) - Confirm immediately
            $update_stmt = $conn->prepare("UPDATE productsorder SET payment_status = 'paid' WHERE order_group_id = ?");
            $update_stmt->bind_param("s", $order_group_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Send Confirmation Email
            require_once __DIR__ . '/confirm_fulfillment.php';
            fulfill_order($order_group_id);

            // Clear the cart
            unset($_SESSION['cart']);
            session_write_close();
            echo "<script>alert('Order Confirmed!'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Please fill in all the required fields.');</script>";
    }
}
?>