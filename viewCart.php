
<?php
session_start();

include 'conn.php';

if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}


$user_id = $_SESSION['user_id'];
$cart = $conn->query("SELECT c.*, p.product_name, p.price FROM cart_items c JOIN product p ON c.product_id = p.product_id WHERE c.user_id = '$user_id'");

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

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

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
            <i class="fas fa-shopping-cart me-2"></i> Your Cart
        </h1>
        <div>
            <a href="add_to_card.php" class="btn btn-outline-primary me-2">
                <i class="fas fa-plus-circle me-1"></i> Add More Items
            </a>
            <a href="invoice.php" class="btn btn-success shadow-sm">
                <i class="fas fa-file-invoice me-1"></i> Proceed to Invoice
            </a>
        </div>
    </div>

    <!-- Cart Table Card -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
            <?php if ($cart->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle table-hover text-center">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col"><i class="fas fa-box me-1"></i> Product</th>
                                <th scope="col"><i class="fas fa-tag me-1"></i> Price (PKR)</th>
                                <th scope="col"><i class="fas fa-sort-numeric-up me-1"></i> Quantity</th>
                                <th scope="col"><i class="fas fa-calculator me-1"></i> Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grand_total = 0;
                            while ($row = $cart->fetch_assoc()):
                                $total = $row['price'] * $row['quantity'];
                                $grand_total += $total;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= number_format($row['price'], 2) ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td class="fw-semibold text-success"><?= number_format($total, 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Grand Total:</td>
                                <td class="text-success fs-5"><?= number_format($grand_total, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Your cart is empty!</h5>
                    <a href="add_to_card.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i> Add Items Now
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

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

</body>

</html>