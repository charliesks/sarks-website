<?php
session_start();
require_once "connection.php"; // central DB connection

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
            $_SESSION["uname"] = $username;
            $_SESSION["uId"] = $cuId;
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
    <title>BMB</title>

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
            <h1 class="logo"><a href="index.html">BMB</a></h1>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="sarks-login.php">Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- Login Section -->
    <section id="services" class="services">
        <div class="container">
            <form class="col-lg-12 col-md-6 align-items-stretch mt-4 mt-md-0" 
                  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" 
                  method="post">
                <div class="icon-box">
                    <div class="icon"><i class="bx bx-user"></i></div>
                    <div class="form-group">
                        <label for="uname" style="font-weight: bold;color: #000;">Username:</label>
                        <input type="text" class="form-control" id="uname" name="uname" required>
                    </div><br>
                    <div class="form-group">
                        <label for="pwd" style="font-weight: bold;color: #000;">Password:</label>
                        <input type="password" class="form-control" id="pwd" name="upass" required>
                    </div><br>
                    <button type="submit" class="btn btn-danger">Login</button>
                    <br><br>
                    <label style="font-weight: bold;color: #000;">Don't have an account?</label>
                    <a href="sarks-cussignup.php" style="font-weight: bold;color: red;">Sign Up</a>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 footer-info">
                        <h3>BMB</h3>
                        <p>2nd floor, Dynagraph Building, Jisr Al Basha Street<br>Hazmieh, Lebanon<br><strong>Phone:</strong> +961 5 428 636<br>
                            <strong>Email:</strong> support@bmbgroup.com<br>
                        </p>
                        <div class="social-links mt-3">
                            <a href="https://www.buymeacoffee.com/sarks"><i class="bx bx-coffee"></i></a>
                            <a href="https://sarks.org"><i class="bx bx-cloud"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#hero">Home</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#about">About us</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#services">Services</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4>Our Services</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#services">Custom URL Blocking</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#services">Ad Blocking</a></li>
                            <li><i class="bx bx-chevron-right"></i> <a href="index.html#services">Malware Protection</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6 footer-newsletter">
                        <h4>Our Newsletter</h4>
                        <p>Please Subscribe to receive our Newsletter</p>
                        <form action="" method="post">
                            <input type="email" name="email"><input type="submit" value="Subscribe">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>BMB</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="https://bmbgroup.com">Charbel Sarkis</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
