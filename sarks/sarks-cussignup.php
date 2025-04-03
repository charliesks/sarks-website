<?php
session_start();
require_once __DIR__ . '/includes/connection.php'; // central DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cuName = $_POST["uname"];
    $cuPassword = $_POST["upass"];
    $cuEmail = $_POST["uemail"];
    $cuMobile = $_POST["umobile"];
    $cuAddress = $_POST["uaddress"];

    // Start a transaction to ensure both inserts succeed
    mysqli_begin_transaction($conn);

    try {
        // Insert into `customer` first
        $stmt1 = $conn->prepare("INSERT INTO customer (cuEmail, cuMobile, cuAddress, cuName) VALUES (?, ?, ?, ?)");
        $stmt1->bind_param("ssss", $cuEmail, $cuMobile, $cuAddress, $cuName);
        $stmt1->execute();

        if ($stmt1->affected_rows > 0) {
            $last_id = $stmt1->insert_id; // Get the generated cuId from `customer`

            // Hash the password before storing it
            $hashed_password = password_hash($cuPassword, PASSWORD_BCRYPT);

            // Insert into `customerlogin` with the correct `cuId`
            $stmt2 = $conn->prepare("INSERT INTO customerlogin (cuUserName, cuPassword, cuId) VALUES (?, ?, ?)");
            $stmt2->bind_param("ssi", $cuName, $hashed_password, $last_id);
            $stmt2->execute();

            if ($stmt2->affected_rows > 0) {
                mysqli_commit($conn); // Commit transaction
                echo "<script>alert('Successfully registered! Redirecting to login...'); window.location.href='sarks-login.php';</script>";
                exit();
            } else {
                throw new Exception("Error inserting into customerlogin.");
            }

            $stmt2->close();
        } else {
            throw new Exception("Error inserting into customer.");
        }

        $stmt1->close();
    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback transaction if an error occurs
        echo "<script>alert('Registration failed: " . $e->getMessage() . "');</script>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sarks - Sign Up</title>

    <!-- Icons -->
    <link href="assets/img/imageedit_1_2859685327.png" rel="icon">

    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Raleway|Poppins" rel="stylesheet">
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
                    <li><a class="nav-link scrollto" href="sarks-login.php">Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>

    <!-- Signup Section -->
    <section id="services" class="services">
        <div class="container">
            <form class="col-lg-12 col-md-6 align-items-stretch mt-4 mt-md-0" 
                  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" 
                  method="post">
                <div class="icon-box">
                    <div class="icon"><i class="bx bx-user-plus"></i></div>
                    <div class="form-group">
                        <label for="user">Username:</label>
                        <input type="text" class="form-control" id="user" name="uname" pattern="[a-zA-Z0-9]+" required>
                    </div><br>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="uemail" required>
                    </div><br>
                    <div class="form-group">
                        <label for="pwd">Password:</label>
                        <input type="password" class="form-control" id="pwd" name="upass" 
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required
                               title="Must contain at least one number, one uppercase, and lowercase letter, and at least 8 characters">
                    </div><br>
                    <div class="form-group">
                        <label for="mbl">Mobile:</label>
                        <input type="text" class="form-control" id="mbl" pattern="[0]{1}[1-9]{1}[0-9]{6}" name="umobile" required>
                    </div><br>
                    <div class="form-group">
                        <label for="adrs">Address:</label>
                        <input type="text" class="form-control" id="adrs" name="uaddress" required>
                    </div><br>
                    <button type="submit" class="btn btn-primary">Sign Up!</button>
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
