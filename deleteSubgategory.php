<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'conn.php';

if (isset($_GET['subcategory_id']) && is_numeric($_GET['subcategory_id'])) {
    $id = intval($_GET['subcategory_id']);

    //  Step 1: Check if subcategory is linked to any products
    $check = $conn->prepare("SELECT COUNT(*) FROM product WHERE subcategory_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        //  Prevent deletion if related products exist
        echo "<script>
                alert('❌ Cannot delete this Subcategory because it is linked to one or more Products.');
                window.location.href = 'subgategory.php';
              </script>";
        exit;
    }

    //  Step 2: Safe to delete
    $stmt = $conn->prepare("DELETE FROM subcategory WHERE subcategory_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert(' Subcategory deleted successfully.');
                window.location.href = 'subgategory.php';
              </script>";
    } else {
        echo "<script>
                alert(' Error deleting Subcategory.');
                window.location.href = 'subgategory.php';
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert(' Invalid request.');
            window.location.href = 'subgategory.php';
          </script>";
}

$conn->close();
?>
