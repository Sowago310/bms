<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BMS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure the fields are properly set and not null
    $vendor_name = isset($_POST["vendor_name"]) ? trim($_POST["vendor_name"]) : '';
    $address = isset($_POST["address"]) ? trim($_POST["address"]) : '';
    $contact = isset($_POST["contact"]) ? trim($_POST["contact"]) : '';
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

    // Debugging: Print received values
    echo "Vendor Name: " . htmlspecialchars($vendor_name) . "<br>";
    echo "Address: " . htmlspecialchars($address) . "<br>";
    echo "Contact: " . htmlspecialchars($contact) . "<br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br>";

    // Check if password meets security requirements
    if (!preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password) ||
        strlen($password) < 8) {
        die("<p style='color:red;'>Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.</p>");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM Vendors WHERE username = ? OR email = ? OR contact = ?");
    $stmt->bind_param("sss", $username, $email, $contact);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("<p style='color:red;'>Username, Email, or Contact already exists.</p><a href='register.html'>Go Back</a>");
    }

    // Insert new user into database
    $stmt = $conn->prepare("INSERT INTO Vendors (vendor_name, address, contact, username, email, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $vendor_name, $address, $contact, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Registration successful!</p><a href='login.php'>Go to Login</a>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    echo "<p style='color:red;'>Form not submitted properly. Please try again.</p>";
}
?>
