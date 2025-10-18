
<?php
session_start();


if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}
include 'conn.php';


if (isset($_POST['addBrand'])) {
    $brand = $_POST['brand'];

    $stmt = $conn->prepare("SELECT * FROM brand WHERE brand_name = ?");
    $stmt->bind_param("s", $brand);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        
        echo "<script>alert('Brand already exists.');</script>";
    } else {
        $stmt1 = $conn->prepare("INSERT INTO brand(brand_name) VALUES(?)");
        $stmt1->bind_param("s", $brand);

        if ($stmt1->execute()) {
            echo "<script>alert('Brand added successfully.');</script>";
        } else {
             echo "<script>alert('Error adding Brand.');</script>";
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

<title>IMS - Brand</title>

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
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-primary fw-bold">
            <i class="fas fa-tags me-2"></i>Brand Management
        </h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addBrandModal">
            <i class="fas fa-plus me-1"></i> Add Brand
        </button>
    </div>

    <!-- Brand Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Brand Name</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = $conn->query("SELECT * FROM brand ORDER BY brand_id DESC");
                        if ($query->num_rows > 0):
                            $count = 1;
                            while ($row = $query->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['brand_name']); ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($row['created_at'] ?? 'now')); ?></td>
                            <td class="text-center">
                                <a href="deleteBrand.php?brand_id=<?= $row['brand_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this brand?')">
                                   <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No brands found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addBrandLabel">
          <i class="fas fa-plus-circle me-1"></i> Add New Brand
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <form method="post">
        <div class="modal-body px-4 py-3">
          <div class="mb-3">
            <label for="brand" class="form-label fw-semibold">Brand Name</label>
            <input type="text" name="brand" id="brand" class="form-control rounded-3" 
                   placeholder="Enter brand name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="addBrand" class="btn btn-primary">
            <i class="fas fa-check me-1"></i> Save
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
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