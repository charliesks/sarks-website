<?php
session_start();
require_once __DIR__ . '/includes/connection.php'; // central DB connection

// Redirect if user is not logged in
if (!isset($_SESSION["uname"])) {
    header("Location: sarks-login.php");
    exit();
}

// Get user details from the database
$stmt = $conn->prepare("SELECT cuId, cuName, cuEmail, cuMobile, cuAddress FROM customer WHERE cuName = ?");
$stmt->bind_param("s", $_SESSION["uname"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($cuId, $cuname, $cuemail, $cumobile, $cuadd);
$stmt->fetch();

if ($stmt->num_rows == 0) {
    echo "<script>alert('User not found! Redirecting to login...'); window.location.href='sarks-login.php';</script>";
    exit();
}

$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newCuName = $_POST["uname"];
    $newCuEmail = $_POST["uemail"];
    $newCuMobile = $_POST["umobile"];
    $newCuAddress = $_POST["uaddress"];
    $newCuPassword = $_POST["upass"];

    // Check if the password field is filled, and hash it before updating
    if (!empty($newCuPassword)) {
        $hashed_password = password_hash($newCuPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE customerlogin SET cuUserName = ?, cuPassword = ? WHERE cuId = ?");
        $stmt->bind_param("ssi", $newCuName, $hashed_password, $cuId);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE customerlogin SET cuUserName = ? WHERE cuId = ?");
        $stmt->bind_param("si", $newCuName, $cuId);
        $stmt->execute();
        $stmt->close();
    }

    // Update customer table
    $stmt = $conn->prepare("UPDATE customer SET cuEmail = ?, cuMobile = ?, cuAddress = ?, cuName = ? WHERE cuId = ?");
    $stmt->bind_param("ssssi", $newCuEmail, $newCuMobile, $newCuAddress, $newCuName, $cuId);
    if ($stmt->execute()) {
        $_SESSION["uname"] = $newCuName; // Update session username
        echo "<script>alert('Successfully Updated!'); window.location.href='sarks-cupanel.php';</script>";
    } else {
        echo "<script>alert('Update Unsuccessful!');</script>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Account Panel - Sarks</title>
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
                    <li><a class="nav-link scrollto" href="index.html">Home</a></li>
                    <li><a class="nav-link scrollto" href="sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
                <button id="music-toggle" class="music-btn">
                    <i class="bi bi-volume-mute-fill"></i>
                </button>
                <audio id="bg-music" loop autoplay>
                    <source src="assets/audio/background.mp3" type="audio/mpeg">
                </audio>
            </nav>

        </div>
    </header><!-- End Header -->

    <main id="main">

        <!-- ======= Account Panel Section ======= -->
        <section id="account" class="d-flex align-items-center" style="min-height: 100vh; padding-top: 80px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="glass-panel p-5" data-aos="fade-up">
                            <div class="section-header mb-4">
                                <h2>Account Details</h2>
                                <p>Update your profile information</p>
                            </div>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="php-email-form">
                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="user" class="mb-2 text-white">Username</label>
                                        <input type="text" class="form-control" id="user" name="uname" value="<?php echo htmlspecialchars($cuname); ?>" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="email" class="mb-2 text-white">Email</label>
                                        <input type="email" class="form-control" id="email" name="uemail" value="<?php echo htmlspecialchars($cuemail); ?>" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="mbl" class="mb-2 text-white">Mobile</label>
                                        <input type="text" class="form-control" id="mbl" name="umobile" value="<?php echo htmlspecialchars($cumobile); ?>" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                    </div>

                                    <div class="col-md-6 form-group mb-3">
                                        <label for="pwd" class="mb-2 text-white">New Password (optional)</label>
                                        <input type="password" class="form-control" id="pwd" name="upass" placeholder="Leave blank to keep current" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="adrs" class="mb-2 text-white">Address</label>
                                    <input type="text" class="form-control" id="adrs" name="uaddress" value="<?php echo htmlspecialchars($cuadd); ?>" required style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn-hero w-100 mb-3">Update Info</button>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="sarks-cushome.php" class="text-primary">Cancel</a>
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

    <script>
        // Simple animation for the panel
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