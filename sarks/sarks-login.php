<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/connection.php'; // central DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["uname"];
    $password = $_POST["upass"];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT cuId, cuPassword FROM customerlogin WHERE cuUserName = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cuId, $db_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Verify the hashed password
        if (password_verify($password, $db_password)) {
            session_regenerate_id(true);
            $_SESSION["uname"] = $username;
            $_SESSION["uId"] = $cuId;
            session_write_close();
            header("Location: sarks-cushome.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<script>alert('Incorrect username or password!');</script>";
        }
    } else {
        echo "<script>alert('Incorrect username or password!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Sarks</title>
    <meta content="Containing Threats at Light Speed" name="description">
    <meta content="Sinkhole, Ad Blocking, Malware Protection" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/sarks-blackhole.png" rel="icon">
    <link href="assets/img/sarks-blackhole.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;700;800&display=swap"
        rel="stylesheet">

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
                    <img src="assets/img/sarks-red.png" alt="Sarks Logo">
                </a>
            </h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="index.html#hero">Home</a></li>
                    <li><a class="nav-link scrollto" href="index.html#about">About</a></li>
                    <li><a class="nav-link scrollto" href="index.html#concepts">Concepts</a></li>
                    <li><a class="nav-link scrollto" href="index.html#elements">Elements</a></li>
                    <li><a class="nav-link scrollto" href="index.html#contact">Contact</a></li>
                    <li><a class="nav-link active" href="sarks-login.php">Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>

            </nav>

        </div>
    </header><!-- End Header -->

    <main id="main">

        <!-- ======= Login Section ======= -->
        <section id="login" class="d-flex align-items-center" style="min-height: 100vh; padding-top: 80px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-8">
                        <div class="glass-panel p-5" data-aos="fade-up">
                            <div class="section-header mb-4">
                                <h2>Login</h2>
                                <p>Access your dashboard</p>
                            </div>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group mb-3">
                                    <label for="uname" class="mb-2 text-white">Username</label>
                                    <input type="text" class="form-control" id="uname" name="uname" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                </div>
                                <div class="form-group mb-4">
                                    <label for="pwd" class="mb-2 text-white">Password</label>
                                    <input type="password" class="form-control" id="pwd" name="upass" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn-hero w-100 mb-3">Login</button>
                                </div>

                                <div class="text-center mt-3">
                                    <p class="text-muted">Don't have an account? <a href="sarks-cussignup.php" class="text-primary">Sign Up</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

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
                        <!--<a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
            <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
            <a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>-->
                        <a href="https://www.linkedin.com/in/charbel-sarkis/" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><i class="bx bx-chevron-right"></i> <a href="index.html#hero">Home</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="index.html#about">About us</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="index.html#concepts">Concepts</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="index.html#elements">Elements</a></li>
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
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>


    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LessB0iAAAAAMlxwDmGOk06_0dbJpLyKku38b86"></script>

    <script>
        // Simple animation for the login card
        gsap.from(".glass-panel", {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: "power3.out",
            delay: 0.2
        });
    </script>

</body>

</html>