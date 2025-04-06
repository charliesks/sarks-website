<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/connection.php'; // central DB connection

// Set the default page
$_page = "products";

// Validate and set the requested page
$allowed_pages = array("products", "cart");
if (isset($_GET['page']) && in_array($_GET['page'], $allowed_pages)) {
    $_page = $_GET['page'];
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>BMB - Catalogue</title>

    <!-- Icons -->
    <link href="assets/img/bmb.png" rel="icon">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- ✅ Boxicons Added -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <style>
        table, th, td {
            padding: 15px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="../index.html">Sarks</a></h1>
            <a href="../index.html" class="logo"><img src="../assets/img/imageedit_1_2859685327.png" alt="" class="img-fluid"></a>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="../sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="../sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- Catalogue Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <span>Catalogue</span>
                <h2>Catalogue</h2>
            </div>

            <div class="row">
                <div class="col-lg-7 col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-purchase-tag"></i></div> <!-- ✅ Icon with Boxicons -->
                        <div id="container">
                            <div id="main">
                                <?php require($_page.".php"); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="fade-up" data-aos-delay="150">
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-cart-download"></i></div> <!-- ✅ Cart Icon with Boxicons -->
                        <h4>Cart</h4>
                        
                        <?php
                        if (!empty($_SESSION['cart'])) {
                            // Create a database connection
                            $conn = mysqli_connect("sarks_mysql", "root", "root", "sarksdb");

                            if (!$conn) {
                                die("Connection Failed: " . mysqli_connect_error());
                            }

                            // Fetch product details
                            $product_ids = implode(",", array_keys($_SESSION['cart']));
                            $sql = "SELECT * FROM products WHERE pdtId IN ($product_ids) ORDER BY pdtId ASC";
                            $query = mysqli_query($conn, $sql);

                            // Display cart items
                            while ($row = mysqli_fetch_array($query)) {
                                echo '<p style="padding:7px;font-weight:bold;font-size:15px;">' . 
                                    htmlspecialchars($row['pdtName']) . ' x ' . 
                                    $_SESSION['cart'][$row['pdtId']]['quantity'] . 
                                    '</p>';
                            }
                            mysqli_close($conn);
                        ?>
                            <hr />
                            <a style="font-weight: bold;color: red;" href="index.php?page=cart">Go to cart</a>
                        <?php
                        } else {
                            echo "<p>Your Cart is empty. Please add some products.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>BMB</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="https://Sarks.org/">Charbel Sarkis</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../assets/vendor/aos/aos.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

</body>
</html>
