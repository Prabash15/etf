<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pg_computers";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get products by category
function getProductsByCategory($conn, $category) {
    $sql = "SELECT * FROM product WHERE category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to generate product cards
function generateProductCards($products) {
    $output = '';
    foreach ($products as $product) {
        $output .= '
        <div class="product-card">
            <img src="' . htmlspecialchars($product['imageURL']) . '" alt="' . htmlspecialchars($product['productName']) . '">
            <h3>' . htmlspecialchars($product['productName']) . '</h3>
            <p>LKR ' . number_format($product['price'], 2) . '</p>
            <button class="add-to-cart">Add to Cart</button>
        </div>';
    }
    return $output;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" href="productpage.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="header">
        <!-- Header content remains the same -->
    </header>
    
    <main>
        <!-- Product Row 1 -->
        <section class="product-row">
            <h2 id="ind">Input Devices</h2>
            <div class="product-container">
                <?php
                $inputDevices = getProductsByCategory($conn, 'Input Devices');
                echo generateProductCards($inputDevices);
                ?>
            </div>
        </section>

        <!-- Product Row 2 -->
        <section class="product-row">
            <h2 id="otd">Output Devices</h2>
            <div class="product-container">
                <?php
                $outputDevices = getProductsByCategory($conn, 'Output Devices');
                echo generateProductCards($outputDevices);
                ?>
            </div>
        </section>

        <!-- Product Row 3 -->
        <section class="product-row">
            <h2 id="std">Storage Devices</h2>
            <div class="product-container">
                <?php
                $storageDevices = getProductsByCategory($conn, 'Storage Devices');
                echo generateProductCards($storageDevices);
                ?>
            </div>
        </section>
    </main>

    <footer class="footer" style="float: left; width: 100%;">
        <!-- Footer content remains the same -->
    </footer>

    <?php $conn->close(); ?>
</body>

</html>