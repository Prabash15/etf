<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="loginform.css">
    <link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>

<body>
    <?php
    session_start(); // Start the session

    // Database configuration
    $servername = "localhost";
    $username = "root"; // Default MySQL username
    $password = ""; // Default MySQL password
    $dbname = "pg_computers";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare SQL statement to select user with matching email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php"); // Replace with the actual admin page
                } else {
                    header("Location: user_dashboard.php"); // Replace with the actual user page
                }
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password!</p>";
            }
        } else {
            echo "<p style='color:red;'>No user found with that email!</p>";
        }

        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>

    <div class="login-box">
        <h1>Login</h1>
        <form action="" method="post">
            <label>Email</label>
            <input type="email" name="email" placeholder="" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="" required>
            <input type="submit" value="Submit" id="signupbutton">
        </form>
    </div>
    <p class="para-3">Don't have an account? <a href="signup.php">Sign up here</a></p>

    <footer class="footer" style="float: left; width: 100%; margin-top: 260px;">
        <div class="footer-container">
            <div class="footer-section about">
                <h2>About Us</h2>
                <p style="text-align: left;">Our mission is to provide you with top-quality components, exceptional
                    customer service, and competitive prices.
                </p>
            </div>
            <div class="footer-section links">
                <h2>Quick Links</h2>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="aboutus.html">About</a></li>
                    <li><a href="payment-method.html">Payment methods</a></li>
                    <li><a href="contactus.html">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h2>Contact Us</h2>
                <ul>
                    <li><strong>Email:</strong> prabashheethanjana@gmail.com</li>
                    <li><strong>Phone:</strong> +94 112244872 / +94 767377939</li>
                    <li><strong>Address:</strong> 123 Nugahena Rd, Niwandama, JaEla</li>
                </ul>
            </div>
            <div class="footer-section social">
                <h2>Follow Us</h2>
                <div class="social-icons">
                    <a href="http://www.facebook.com"><i class="fa-brands fa-square-facebook fa-lg"
                            style="color: white;"></i></a>
                    <a href="http://www.x.com"><i class="fa-brands fa-x-twitter fa-lg" style="color: white;"></i></a>
                    <a href="http://www.instagram.com"><i class="fa-brands fa-instagram fa-lg" style="color: white;"></i></a>
                    <a href="http://www.linkedin.com"><i class="fa-brands fa-linkedin fa-lg" style="color: white;"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
