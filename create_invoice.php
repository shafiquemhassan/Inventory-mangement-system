<?php
session_start();
include 'conn.php';

if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_no = $_POST['invoice_id'];
    $customer_name = $_POST['customer_name'];
    $customer_mobile = $_POST['customer_mobile'];
    $total_amount = $_POST['total_amount'];

    // 1. Insert into invoices table
    $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, customer_name, mobile, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $invoice_no, $customer_name, $customer_mobile, $total_amount);

    if ($stmt->execute()) {
        $invoice_id = $conn->insert_id;

        // 2. Get cart items
        $cart_stmt = $conn->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        // 3. Insert each item into invoice_items
        $item_stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity) VALUES (?, ?, ?)");

        while ($row = $cart_result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $item_stmt->bind_param("iii", $invoice_id, $product_id, $quantity);
            $item_stmt->execute();
        }

        // 4. Clear cart
        $clear_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();

        // 5. Redirect
        echo "<script>alert('Invoice created successfully.'); window.location.href = 'view_invoice.php?id=$invoice_id';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to create invoice.'); window.history.back();</script>";
    }
}
?>