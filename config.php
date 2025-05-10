
<?php
// Database connection configuration
$server = "localhost";
$username = "root";
$password = "";
$database = "tokoshop";

// Create connection
$conn = mysqli_connect($server, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Site constants
define("SITE_NAME", "TokoShop");
define("CURRENCY", "Rp");
define("UPLOAD_PATH", "uploads/");

// Session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function clean($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>
