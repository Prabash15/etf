<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="loginform.css">
    <link rel="stylesheet" href="styles.css">
    <title>Sign Up</title>
</head>

<body>
    <?php
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
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate passwords match
        if ($password !== $confirm_password) {
            echo "<p style='color:red;'>Passwords do not match!</p>";
        } else {
            // Check if email already exists
            $email_check_sql = "SELECT email FROM users WHERE email = ?";
            $stmt = $conn->prepare($email_check_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "<p style='color:red;'>An account with this email already exists!</p>";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare SQL statement to insert data
                $sql = "INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'user')";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    // Bind parameters and execute the statement
                    $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);
                    if ($stmt->execute()) {
                        echo "<p style='color:green;'>Registration successful!</p>";
                    } else {
                        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
                }
            }
        }
    }

    // Close the database connection
    $conn->close();
    ?>

    <div class="signup-box">
        <h1>Sign Up</h1>
        <form action="" method="post">
            <label>First name</label>
            <input type="text" name="first_name" placeholder="" required>
            <label>Last name</label>
            <input type="text" name="last_name" placeholder="" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="" required>
            <label>Confirm password</label>
            <input type="password" name="confirm_password" placeholder="" required>
            <input type="submit" value="Sign Up" id="signupbutton">
        </form>
        <p>By clicking the sign up button, you agree to our <br>
            <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
        </p>
    </div>

    <p class="para-3">Already have an account? <a href="login.html">Login here</a></p>

    <footer class="footer" style="float: left; width: 100%;">
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
        <div class="footer-bottom">
            <p>&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
