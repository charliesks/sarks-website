<?php
require_once __DIR__ . '/includes/session_ready.php';
require_once __DIR__ . '/includes/connection.php';

$message = "";
$message_type = "success";

// Add item to cart logic
if (isset($_GET['action']) && $_GET['action'] == "add" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if (!isset($_SESSION['uname'])) {
        // Redirection handled by the button link if not logged in
        // But as a fallback/security check:
        header("Location: sarks-login.php?redirect=sarks-products.php?action=add&id=$id");
        exit();
    }

    // Fetch product details from database
    $stmt = $conn->prepare("SELECT * FROM products WHERE pdtId = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = array(
                "quantity" => 1,
                "price" => $row['price']
            );
        }
        $message = "<strong>" . htmlspecialchars($row['pdtName']) . "</strong> has been added to your cart!";
    } else {
        $message = "Invalid product ID!";
        $message_type = "danger";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Our Plans - Sarks</title>
  <meta content="Choose the best protection plan for your needs." name="description">
  <meta content="Plans, Bronze, Silver, Gold, Protection Plans" name="keywords">

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
  
  <style>
    .pricing-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .pricing-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(255, 0, 0, 0.2);
    }
    .pricing-card.featured {
      border: 2px solid #ff0000;
      transform: scale(1.05);
      z-index: 10;
    }
    .pricing-card.featured:hover {
      transform: scale(1.05) translateY(-10px);
    }
    .price {
      font-size: 3rem;
      font-weight: 800;
      color: #ff0000;
      margin-bottom: 1.5rem;
    }
    .price span {
      font-size: 1rem;
      color: #aaa;
      font-weight: 400;
    }
    .features-list {
      list-style: none;
      padding: 0;
      margin-bottom: 2rem;
      text-align: left;
      flex-grow: 1;
    }
    .features-list li {
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
    }
    .features-list li i {
      color: #ff0000;
      margin-right: 10px;
    }
    .plan-badge {
      position: absolute;
      top: 15px;
      right: -35px;
      background: #ff0000;
      color: white;
      padding: 5px 40px;
      transform: rotate(45deg);
      font-size: 0.8rem;
      font-weight: 600;
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">

      <h1 class="logo">
        <a href="index.php">
          <img src="assets/img/sarks-red.png" alt="Sarks Logo">
        </a>
      </h1>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto" href="index.php#hero">Home</a></li>
          <li><a class="nav-link active" href="sarks-products.php">Plans</a></li>
          <li><a class="nav-link scrollto" href="index.php#contact">Contact</a></li>
          <?php if (isset($_SESSION['uname'])): ?>
            <li><a class="nav-link" href="cart/index.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>
            <li><a class="nav-link" href="sarks-cushome.php">Dashboard</a></li>
          <?php else: ?>
            <li><a class="nav-link" href="sarks-login.php">Login</a></li>
          <?php endif; ?>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>

    </div>
  </header><!-- End Header -->

  <main id="main">
    <section id="pricing" class="about section-bg d-flex align-items-center" style="min-height: 100vh; padding: 100px 0;">
      <div class="container">

        <?php if ($message != ""): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show glass-panel mb-5" role="alert" style="border: 1px solid rgba(255,255,255,0.1);">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
            <div class="mt-2">
              <a href="cart/index.php" class="btn btn-sm btn-hero">Go to Cart</a>
            </div>
          </div>
        <?php endif; ?>

        <div class="section-header">
          <h2>Our Plans</h2>
          <p>Choose the level of security that fits your requirements.</p>
        </div>

        <div class="row gy-4 align-items-stretch">

          <!-- Bronze Plan -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="glass-panel pricing-card p-5">
              <h3>Bronze Protection</h3>
              <div class="price">0$<span>/yr</span></div>
              <ul class="features-list">
                <li><i class="bi bi-check-circle-fill"></i> Standard Ad Blocking</li>
                <li><i class="bi bi-check-circle-fill"></i> Malware Protection</li>
                <li><i class="bi bi-check-circle-fill"></i> Community Support</li>
                <li class="text-muted"><i class="bi bi-x-circle-fill"></i> No Custom URL Lists</li>
                <li class="text-muted"><i class="bi bi-x-circle-fill"></i> Standard Speed</li>
              </ul>
              <?php 
                $bronze_id = 1;
                $buy_url = isset($_SESSION['uname']) 
                  ? "sarks-products.php?action=add&id=$bronze_id" 
                  : "sarks-login.php?redirect=" . urlencode("sarks-products.php?action=add&id=$bronze_id");
              ?>
              <a href="<?php echo $buy_url; ?>" class="btn-hero w-100">Buy Now (Free)</a>
            </div>
          </div>

          <!-- Silver Plan (Featured) -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="glass-panel pricing-card featured p-5">
              <div class="plan-badge">Popular</div>
              <h3>Silver Protection</h3>
              <div class="price">25$<span>/yr</span></div>
              <ul class="features-list">
                <li><i class="bi bi-check-circle-fill"></i> Advanced Ad Blocking</li>
                <li><i class="bi bi-check-circle-fill"></i> Premium Malware Shield</li>
                <li><i class="bi bi-check-circle-fill"></i> Custom URL Filtering (Up to 10)</li>
                <li><i class="bi bi-check-circle-fill"></i> Priority Email Support</li>
                <li><i class="bi bi-check-circle-fill"></i> High Speed DNS</li>
              </ul>
              <?php 
                $silver_id = 2;
                $buy_url = isset($_SESSION['uname']) 
                  ? "sarks-products.php?action=add&id=$silver_id" 
                  : "sarks-login.php?redirect=" . urlencode("sarks-products.php?action=add&id=$silver_id");
              ?>
              <a href="<?php echo $buy_url; ?>" class="btn-hero w-100">Buy Now (25$)</a>
            </div>
          </div>

          <!-- Gold Plan -->
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="glass-panel pricing-card p-5">
              <h3>Gold Protection</h3>
              <div class="price">50$<span>/yr</span></div>
              <ul class="features-list">
                <li><i class="bi bi-check-circle-fill"></i> Ultimate Ad Blocking</li>
                <li><i class="bi bi-check-circle-fill"></i> Real-time Threat Intelligence</li>
                <li><i class="bi bi-check-circle-fill"></i> Unlimited Custom Filters</li>
                <li><i class="bi bi-check-circle-fill"></i> 24/7 Dedicated Support</li>
                <li><i class="bi bi-check-circle-fill"></i> Ultra-Low Latency DNS</li>
              </ul>
              <?php 
                $gold_id = 3;
                $buy_url = isset($_SESSION['uname']) 
                  ? "sarks-products.php?action=add&id=$gold_id" 
                  : "sarks-login.php?redirect=" . urlencode("sarks-products.php?action=add&id=$gold_id");
              ?>
              <a href="<?php echo $buy_url; ?>" class="btn-hero w-100">Buy Now (50$)</a>
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
            <li><i class="bx bx-chevron-right"></i> <a href="index.php#hero">Home</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="index.php#about">About us</a></li>
            <li><i class="bx bx-chevron-right"></i> <a href="sarks-products.php">Plans</a></li>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

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

</body>

</html>
