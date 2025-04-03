<?php
session_start();
require_once "connection.php"; // central DB connection

// Redirect if user is not logged in
if (!isset($_SESSION["uname"])) {
    header("Location: sarks-login.php");
    exit();
}

// Use a prepared statement to prevent SQL injection
$stmt = $con->prepare("SELECT cuId, cuPassword FROM customerlogin WHERE cuUserName = ?");
$stmt->bind_param("s", $_SESSION["uname"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($cuId, $stored_password);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    $_SESSION["uId"] = $cuId; // Store user ID in session
} else {
    echo "<script>alert('User not found! Redirecting to login...'); window.location.href='sarks-login.php';</script>";
    exit();
}

$stmt->close();
$con->close();
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sarks - Dashboard</title>

    <!-- Icons -->
    <link href="assets/img/imageedit_1_2859685327.png" rel="icon">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Header -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="index.html">Sarks</a></h1>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- Dashboard Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <span>Dashboard</span>
                <h2>Dashboard</h2>
                <p>Here is what we can do for you.</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up">
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-cart-add"></i></div>
                        <h4><a href="cart/index.php">Browse Products</a></h4>
                        <p>Check the list of available products in our store.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="fade-up" data-aos-delay="150">
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-user-circle"></i></div>
                        <h4><a href="sarks-cupanel.php">Account Details</a></h4>
                        <p>Manage and review your existing profile information.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-box">
                        <div class="icon"><i class="bx bx-cart"></i></div>
                        <h4><a href="cart/index.php">Cart</a></h4>
                        <p>Check and manage your current selected products in your cart</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>SARKS</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="https://Sarks.org/">Charbel Sarkis</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
