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

<body class="bg-light">

    <div class="container py-5">

        <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-primary fw-bold mb-1">
                            <i class="fas fa-file-invoice me-2"></i>Invoice
                        </h2>
                        <p class="text-muted mb-0">#<?= htmlspecialchars($invoice['invoice_number']) ?></p>
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary">Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($invoice['customer_name']) ?></p>
                        <p class="mb-1"><strong>Mobile:</strong> <?= htmlspecialchars($invoice['mobile']) ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="fw-bold text-secondary">Invoice Details</h6>
                        <p class="mb-1"><strong>Date:</strong> <?= date('d M Y', strtotime($invoice['created_at'] ?? 'now')) ?></p>
                        <p class="mb-1"><strong>Total:</strong> ₹<?= number_format($invoice['total_amount'], 2) ?></p>
                    </div>
                </div>

                <!-- Items Table -->
                <h5 class="fw-bold text-secondary mb-3">
                    <i class="fas fa-shopping-basket me-1"></i> Items
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price (₹)</th>
                                <th>Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal = 0;
                            while ($row = $item_result->fetch_assoc()):
                                $total = $row['price'] * $row['quantity'];
                                $subtotal += $total;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td><?= number_format($row['price'], 2) ?></td>
                                    <td class="fw-semibold text-success"><?= number_format($total, 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-success fs-5"><?= number_format($subtotal, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="text-center mt-5 border-top pt-3">
                    <p class="text-muted small mb-0">
                        Thank you for your purchase!
                    </p>
                    <p class="text-muted small mb-0">
                        © <?= date('Y') ?> Inventory Management System
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Print Optimization -->
    <style>
        @media print {
            body {
                background: #fff !important;
            }
            .btn, .navbar, a[href] {
                display: none !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

</body>


</html>