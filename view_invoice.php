<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    echo "<script>alert('Invalid request.'); window.location.href = 'index.php';</script>";
    exit;
}

$invoice_id = $_GET['id'];


$invoice_stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ?");
$invoice_stmt->bind_param("i", $invoice_id);
$invoice_stmt->execute();
$invoice_result = $invoice_stmt->get_result();

if ($invoice_result->num_rows === 0) {
    echo "<script>alert('Invoice not found.'); window.location.href = 'index.php';</script>";
    exit;
}

$invoice = $invoice_result->fetch_assoc();

// 2. Get invoice items
$item_stmt = $conn->prepare("
    SELECT ii.*, p.product_name, p.price 
    FROM invoice_items ii 
    JOIN product p ON ii.product_id = p.product_id 
    WHERE ii.invoice_id = ?
");
$item_stmt->bind_param("i", $invoice_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body>
    <div class="main-content">
        <h2>Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($invoice['customer_name']) ?></p>
        <p><strong>Mobile:</strong> <?= htmlspecialchars($invoice['mobile']) ?></p>
        <p><strong>Total Amount:</strong> ₹<?= number_format($invoice['total_amount'], 2) ?></p>

        <h4>Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $item_result->fetch_assoc()):
                    $total = $row['price'] * $row['quantity'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>₹<?= number_format($row['price'], 2) ?></td>
                        <td>₹<?= number_format($total, 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
        <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/bootstrap.js"></script>
</body>

</html>