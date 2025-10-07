<?php
include 'conn.php'; // DB connection

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Optionally validate the ID
    if (is_numeric($id)) {
        $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Product deleted successfully'); window.location.href = 'product.php ';</script>";
        } else {
            echo "<script>alert('Error deleting product'); window.history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid ID'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No ID provided'); window.history.back();</script>";
}
?>
