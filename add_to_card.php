
<?php
session_start();


if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}
include 'conn.php';
$message = '';
if (isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'];
    $product = (int) $_POST['product'];
    $quantity = (int) $_POST['quantity'];

    if ($product > 0 && $quantity > 0) {
    
        $check = $conn->prepare("SELECT id FROM cart_items WHERE user_id = ? AND product_id = ?");
        $check->bind_param("ii", $user_id, $product);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product, $quantity);
        }

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success alert-dismissible fade show mt-3' role='alert'>
                    Product added to cart successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show mt-3' role='alert'>
                    Failed to add product to cart.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }

        $stmt->close();
    }
}

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
<div class="container-fluid">

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h1 class="h3 mb-0 text-gray-800">Add To Cart</h1>
</div>
<form action="" method="post">
    <div class="my-3">
        <label class="form-label">Product</label>
        <select name="product" class="form-control" required>
            <option value="">Select Product</option>
            <?php
            $query = $conn->prepare("SELECT * FROM product");
            $query->execute();
            $result = $query->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['product_id']) . "'>" . htmlspecialchars($row['product_name']) . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="my-3">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>

    <button class="btn btn-primary" name="add_to_cart">Add to cart</button>
    <a href="viewCart.php" class="btn btn-success">View Cart</a>
</form>

<!-- Alert message -->
<?php if (!empty($message)) {
    echo $message;
} ?>


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