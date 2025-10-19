<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'conn.php';

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $id = intval($_GET['category_id']);

    // Check if this category is used in any products
    $check = $conn->prepare("SELECT COUNT(*) FROM product WHERE category_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>
                alert(' Cannot delete this category because it is linked to one or more products.');
                window.location.href = 'gategory.php';
              </script>";
        exit;
    }

    // Otherwise safe to delete
    $stmt = $conn->prepare("DELETE FROM category WHERE category_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert(' Category deleted successfully.');
                window.location.href = 'gategory.php';
              </script>";
    } else {
        echo "<script>
                alert(' Error deleting category.');
                window.location.href = 'gategory.php';
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('Invalid request.');
            window.location.href = 'gategory.php';
          </script>";
}

$conn->close();
?>
