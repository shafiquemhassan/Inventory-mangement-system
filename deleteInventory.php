<?php
session_start();
include 'conn.php';

// Redirect if user not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Check if ID is passed via GET
if (isset($_GET['id'])) {
    $inventory_id = intval($_GET['id']); // Prevent SQL injection

    // Check if the record exists before deleting
    $check = $conn->prepare("SELECT * FROM inventory WHERE inventory_id = ?");
    $check->bind_param("i", $inventory_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Proceed with deletion
        $stmt = $conn->prepare("DELETE FROM inventory WHERE inventory_id = ?");
        $stmt->bind_param("i", $inventory_id);

        if ($stmt->execute()) {
            echo "<script>alert('Inventory item deleted successfully!'); window.location.href='inventory.php';</script>";
        } else {
            echo "<script>alert('Error deleting record.'); window.location.href='inventory.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid inventory ID.'); window.location.href='inventory.php';</script>";
    }

    $check->close();
} else {
    // If ID not set
    echo "<script>alert('No inventory ID provided.'); window.location.href='inventory.php';</script>";
}

$conn->close();
?>
