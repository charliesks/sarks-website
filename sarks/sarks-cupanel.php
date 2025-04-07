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
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>BMB - Account Panel</title>

    <!-- Icons -->
    <link href="assets/img/bmbwhite.png" rel="icon">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
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
                    <li><a class="nav-link scrollto" href="sarks-cushome.php">Dashboard</a></li>
                    <li><a class="nav-link scrollto" href="sarks-logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- Account Details Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <span>Account Details</span>
                <h2>Account Details</h2>
            </div>

            <form class="col-lg-12 col-md-6 align-items-stretch mt-4 mt-md-0" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="icon-box">
                    <div class="icon"><i class="bx bx-user-check"></i></div>

                    <div class="form-group center">
                        <label for="user">Username:</label>
                        <input type="text" class="form-control" id="user" name="uname" value="<?php echo htmlspecialchars($cuname); ?>" required>
                    </div><br>

                    <div class="form-group center">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="uemail" value="<?php echo htmlspecialchars($cuemail); ?>" required>
                    </div><br>

                    <div class="form-group">
                        <label for="pwd">New Password (optional):</label>
                        <input type="password" class="form-control" id="pwd" name="upass" placeholder="Leave blank to keep current password">
                    </div><br>

                    <div class="form-group">
                        <label for="mbl">Mobile:</label>
                        <input type="text" class="form-control" id="mbl" name="umobile" value="<?php echo htmlspecialchars($cumobile); ?>" required>
                    </div><br>

                    <div class="form-group">
                        <label for="adrs">Address:</label>
                        <input type="text" class="form-control" id="adrs" name="uaddress" value="<?php echo htmlspecialchars($cuadd); ?>" required>
                    </div><br>

                    <button type="submit" class="btn btn-primary">Update Info!</button>
                    <br><br>
                    <a href="sarks-login.php">Go back to Login</a>
                </div>
            </form>
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
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>
