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
    <title>Catalogue - Sarks</title>
    <meta content="Containing Threats at Light Speed" name="description">
    <meta content="Sinkhole, Ad Blocking, Malware Protection" name="keywords">

    <!-- Favicons -->
    <link href="../assets/img/sarks-blackhole.png" rel="icon">
    <link href="../assets/img/sarks-blackhole.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- ======= Header ======= -->
    <!-- ======= Header ======= -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo">
                <a href="../index.html">
                    <img src="../assets/img/sarks-red.png" alt="Sarks Logo">
                </a>
            </h1>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="../index.html">Home</a></li>
                    <li><a class="nav-link scrollto" href="../sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="../sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
                <button id="music-toggle" class="music-btn">
                    <i class="bi bi-volume-mute-fill"></i>
                </button>
                <audio id="bg-music" loop autoplay>
                    <source src="../assets/audio/background.mp3" type="audio/mpeg">
                </audio>
            </nav>
        </div>
    </header><!-- End Header -->

    <!-- ======= Catalogue Section ======= -->
    <section id="catalogue" class="d-flex align-items-center" style="min-height: 100vh; padding-top: 80px;">
        <div class="container">
            <div class="section-header">
                <h2>Catalogue</h2>
                <p>Browse our products</p>
            </div>

            <div class="row">
                <!-- Main Content Area -->
                <div class="col-lg-8 col-md-7 mb-4" data-aos="fade-up">
                    <div class="glass-panel p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-purchase-tag fs-3 text-primary me-2"></i>
                            <h3 class="m-0">Products</h3>
                        </div>
                        <div id="container">
                            <div id="main">
                                <?php require($_page . ".php"); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Sidebar -->
                <div class="col-lg-4 col-md-5" data-aos="fade-up" data-aos-delay="150">
                    <div class="glass-panel p-4 h-100">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-cart-download fs-3 text-primary me-2"></i>
                            <h3 class="m-0">Your Cart</h3>
                        </div>

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
                            echo '<div class="cart-items mb-3">';
                            while ($row = mysqli_fetch_array($query)) {
                                echo '<div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: rgba(255,255,255,0.05); border-radius: 8px;">';
                                echo '<span class="text-white">' . htmlspecialchars($row['pdtName']) . '</span>';
                                echo '<span class="badge bg-primary">' . $_SESSION['cart'][$row['pdtId']]['quantity'] . '</span>';
                                echo '</div>';
                            }
                            echo '</div>';
                            mysqli_close($conn);
                        ?>
                            <hr class="border-secondary" />
                            <div class="text-center">
                                <a href="index.php?page=cart" class="btn-hero btn-sm w-100">Go to Cart</a>
                            </div>
                        <?php
                        } else {
                            echo "<p class='text-muted'>Your Cart is empty. Please add some products.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======= Footer ======= -->
    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 footer-info">
                    <h3>Sarks</h3>
                    <p>
                        1st floor, Building 95<br>
                        Jisr Al Basha Street, Hazmieh, Lebanon<br><br>
                        <strong>Phone:</strong> +961 03 597 498<br>
                        <strong>Email:</strong> support@sarks.org<br>
                    </p>
                    <div class="social-links mt-3">
                        <a href="https://www.linkedin.com/in/charbel-sarkis/" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bx bx-chevron-right"></i> <a href="../index.html#hero">Home</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="../index.html#about">About us</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="../index.html#concepts">Concepts</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="../index.html#elements">Elements</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 footer-links">
                    <h4>Our Services</h4>
                    <ul>
                        <li><i class="bx bx-chevron-right"></i> <a href="https://www.linkedin.com/in/charbel-sarkis/">Web Design</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="https://www.linkedin.com/in/charbel-sarkis/">Web Development</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="https://www.linkedin.com/in/charbel-sarkis/">IT Security</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="https://www.linkedin.com/in/charbel-sarkis/">IT Support & Outsourcing</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6 footer-newsletter">
                    <h4>Our Newsletter</h4>
                    <p>Subscribe to our newsletter to receive the latest updates.</p>
                    <form action="" method="post">
                        <input type="email" name="email"><input type="submit" value="Subscribe">
                    </form>
                </div>
            </div>

            <div class="copyright">
                &copy; Copyright <strong><span>Sarks</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- GSAP Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="../assets/js/main.js"></script>

    <script>
        // Animate panels
        gsap.from(".glass-panel", {
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