
<?php
session_start();


if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}
include 'conn.php';


if (isset($_POST['addGategory'])) {
    $gategory = $_POST['gategory'];

    $stmt = $conn->prepare("SELECT * FROM category WHERE category_name = ?");
    $stmt->bind_param("s", $gategory);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        
        echo "<script>alert('Gategory already exists.');</script>";
    } else {
        $stmt1 = $conn->prepare("INSERT INTO category(category_name) VALUES(?)");
        $stmt1->bind_param("s", $gategory);

        if ($stmt1->execute()) {
            echo "<script>alert('Gategory added successfully.');</script>";
        } else {
             echo "<script>alert('Error adding Gategory.');</script>";
        }

        $stmt1->close();
    }

    $stmt->close();
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

<title>IMS - Gategory</title>

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
<h1 class="h3 mb-0 text-gray-800">Gategory</h1>
</div>
<form action="" method="post">
<div class="my-3">
<label for="" class="form-label">Gategory Name</label>
<input type="text" name="gategory" id="gategory" class="form-control" placeholder="Please Enter Gategory Name">
</div>
<div class="my-3">
<button class="btn btn-primary" name="addGategory">Add</button>
</div>

</form>
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