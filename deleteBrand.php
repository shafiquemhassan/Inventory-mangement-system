<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'conn.php'; // database connection

// Check if ID is provided
if (isset($_GET['brand_id']) && is_numeric($_GET['brand_id'])) {
    $brand_id = intval($_GET['brand_id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM brand WHERE brand_id = ?");
    $stmt->bind_param("i", $brand_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Brand deleted successfully.');
                window.location.href = 'brand.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting brand.');
                window.location.href = 'brand.php';
              </script>";
    }

    $stmt->close();
} else {
    // Invalid request
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'brand.php';
          </script>";
}

$conn->close();
?>
