<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login page if not admin
    exit();
}

$conn = new mysqli("localhost", "root", "", "pg_computers");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        handleAdd($conn);
    } elseif (isset($_POST['update'])) {
        handleUpdate($conn);
    } elseif (isset($_POST['delete'])) {
        handleDelete($conn);
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM product");
$products = $result->fetch_all(MYSQLI_ASSOC);

function handleAdd($conn) {
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $imageURL = handleImageUpload();
    
    if ($imageURL) {
        $stmt = $conn->prepare("INSERT INTO product (productName, price, imageURL) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $productName, $price, $imageURL);
        $stmt->execute();
        $stmt->close();
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error uploading image.";
    }
}

function handleUpdate($conn) {
    $productId = $_POST['product_id'];
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $imageURL = handleImageUpload();
    
    if ($imageURL) {
        $stmt = $conn->prepare("UPDATE product SET productName = ?, price = ?, imageURL = ? WHERE product_id = ?");
        $stmt->bind_param("sdsi", $productName, $price, $imageURL, $productId);
    } else {
        $stmt = $conn->prepare("UPDATE product SET productName = ?, price = ? WHERE product_id = ?");
        $stmt->bind_param("sdi", $productName, $price, $productId);
    }
    
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_dashboard.php");
    exit();
}

function handleDelete($conn) {
    $productId = $_POST['product_id'];
    
    // First, get the image URL to delete the file
    $stmt = $conn->prepare("SELECT imageURL FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if ($product && file_exists($product['imageURL'])) {
        unlink($product['imageURL']);
    }
    
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->close();
    
    header("Location: admin_dashboard.php");
    exit();
}

function handleImageUpload() {
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "adminimage/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                // Generate a unique filename to avoid overwriting
                $uniqueFileName = uniqid() . '_' . $fileName;
                $targetFile = $targetDir . $uniqueFileName;
                
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    return $targetFile; // Return the relative path
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            echo "File is not an image.";
        }
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    
    <!-- Logout Form -->
    <form action="" method="post" style="float: right;">
        <input type="submit" name="logout" value="Logout">
    </form>
    
    <h2>Add New Product</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="productName" placeholder="Product Name" required>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" name="add" value="Add Product">
    </form>

    <h2>Product List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product['product_id']; ?></td>
            <td><?php echo htmlspecialchars($product['productName']); ?></td>
            <td><?php echo $product['price']; ?></td>
            <td><img src="<?php echo htmlspecialchars($product['imageURL']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>" width="100"></td>
            <td>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="text" name="productName" value="<?php echo htmlspecialchars($product['productName']); ?>" required>
                    <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required>
                    <input type="file" name="image" accept="image/*">
                    <input type="submit" name="update" value="Update">
                </form>
                <form action="" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="submit" name="delete" value="Delete">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>