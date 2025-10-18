<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check if this product is used in inventory
    $check = $conn->prepare("SELECT COUNT(*) FROM inventory WHERE product_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('Cannot delete: This product is used in inventory!'); window.location.href='product.php';</script>";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Product deleted successfully'); window.location.href='product.php';</script>";
}
?>
