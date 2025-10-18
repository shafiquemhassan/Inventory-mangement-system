
<?php
session_start();


if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}
include 'conn.php';

$user_id = $_SESSION['user_id'];
$message = "";


$cart_query = $conn->prepare("
    SELECT c.*, p.product_name, p.price 
    FROM cart_items c 
    JOIN product p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

$invoiceNumber = 'IMS-' . date('Ymd') . '-' . rand(1000, 9999);

?>




<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title>IMS - Dashboard</title>

<!-- Custom fonts for this template-->
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link
href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
rel="stylesheet">

<!-- Custom styles for this template-->
<link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

<?php include 'slide.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
<i class="fa fa-bars"></i>
</button>
<!-- Topbar Navbar -->
<ul class="navbar-nav ml-auto">
<div class="topbar-divider d-none d-sm-block"></div>
<a href="logout.php" class="btn btn-primary">Logout</a>
</ul>
</nav>
<!-- End of Topbar -->

<!-- Begin Page Content -->
<!-- Begin Page Content -->
<div class="container-fluid px-4 py-4">

    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">
            <i class="fas fa-file-invoice me-2"></i> Generate Invoice
        </h1>
        <a href="viewCart.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Cart
        </a>
    </div>

    <!-- Invoice Form Card -->
    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4">

            <form action="create_invoice.php" method="post" class="needs-validation" novalidate>

                <!-- Invoice & Customer Info -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1"></i> Invoice Number
                        </label>
                        <input type="text" name="invoice_id" value="<?php echo $invoiceNumber; ?>"
                            class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-user me-1"></i> Customer Name
                        </label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Enter full name" required>
                        <div class="invalid-feedback">Please enter customer name.</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-phone-alt me-1"></i> Mobile Number
                        </label>
                        <input type="text" name="customer_mobile" class="form-control" placeholder="03XXXXXXXXX" required>
                        <div class="invalid-feedback">Please enter mobile number.</div>
                    </div>
                </div>

                <!-- Cart Items Section -->
                <h5 class="fw-bold text-secondary mb-3">
                    <i class="fas fa-shopping-bag me-1"></i> Cart Summary
                </h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price (PKR)</th>
                                <th>Total (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand_total = 0;
                            while ($item = $cart_result->fetch_assoc()):
                                $total = $item['quantity'] * $item['price'];
                                $grand_total += $total;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($item['price'], 2) ?></td>
                                    <td class="fw-semibold text-success"><?= number_format($total, 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-success fs-5"><?= number_format($grand_total, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Hidden Grand Total -->
                <input type="hidden" name="total_amount" value="<?= $grand_total ?>">

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Generate Invoice
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Form Validation Script -->
<script>
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {

</div>


<!-- Footer -->
<footer class="sticky-footer bg-white">
<div class="container my-auto">
<div class="copyright text-center my-auto">
<span>Copyright &copy; Your Website 2021</span>
</div>
</div>
</footer>
</div>
</div>





<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>