<?php
session_start();
require_once __DIR__ . '/includes/connection.php'; // central DB connection

// Redirect if user is not logged in
if (!isset($_SESSION["uname"])) {
    header("Location: sarks-login.php");
    exit();
}

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT cuId, cuPassword FROM customerlogin WHERE cuUserName = ?");
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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard - BMB</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/bmb.png" rel="icon">
    <link href="assets/img/bmb.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">

            <h1 class="logo">
                <a href="index.html">
                    <img src="assets/img/bmbwhite.png" alt="BMB Logo">
                </a>
            </h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>

        </div>
    </header><!-- End Header -->

    <main id="main">

        <!-- ======= Dashboard Section ======= -->
        <section id="dashboard" class="d-flex align-items-center" style="min-height: 100vh; padding-top: 80px;">
            <div class="container">

                <div class="section-header">
                    <h2>Dashboard</h2>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION["uname"]); ?></p>
                </div>

                <div class="card-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-cart-add"></i></div>
                        <h3><a href="cart/index.php" class="stretched-link">Browse Products</a></h3>
                        <p>Check the list of available products in our store.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-user-circle"></i></div>
                        <h3><a href="sarks-cupanel.php" class="stretched-link">Account Details</a></h3>
                        <p>Manage and review your existing profile information.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon"><i class="bx bx-cart"></i></div>
                        <h3><a href="cart/index.php" class="stretched-link">View Cart</a></h3>
                        <p>Check and manage your current selected products.</p>
                    </div>
                </div>

            </div>
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>BMB</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="https://bmbgroup.com">Charbel Sarkis</a>
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- GSAP Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        // Animate dashboard cards
        gsap.from(".feature-card", {
            duration: 0.8,
            y: 50,
            opacity: 0,
            stagger: 0.2,
            ease: "power3.out",
            delay: 0.2
        });
    </script>

</body>

</html>