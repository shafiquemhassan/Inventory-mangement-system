
<?php
session_start();

include 'conn.php';

if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['addInventory'])) {
    $product_id = $_POST['product'];
    $quantity = $_POST['quantity'];

    if (!empty($product_id) && !empty($quantity)) {
       
        $check = $conn->prepare("SELECT * FROM inventory WHERE product_id = ?");
        $check->bind_param("i", $product_id);
        $check->execute();
        $existing = $check->get_result();

        if ($existing->num_rows > 0) {
            
            $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity + ? WHERE product_id = ?");
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            
            $stmt = $conn->prepare("INSERT INTO inventory (product_id, quantity) VALUES (?, ?)");
            $stmt->bind_param("ii", $product_id, $quantity);
            $stmt->execute();
            $stmt->close();
        }

        echo "<script>alert('Inventory added/updated successfully'); window.location.href='inventory.php';</script>";
    } else {
        echo "<script>alert('Please select product and enter quantity');</script>";
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
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-primary fw-bold">
            <i class="fas fa-boxes me-2"></i>Inventory Management
        </h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addInventoryModal">
            <i class="fas fa-plus me-1"></i> Add Inventory
        </button>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Last Updated</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT i.inventory_id, p.product_name, i.quantity, i.last_updated
                                  FROM inventory i
                                  JOIN product p ON i.product_id = p.product_id
                                  ORDER BY i.last_updated DESC";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0):
                            $count = 1;
                            while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['product_name']); ?></td>
                            <td><span class="badge bg-info text-dark px-3"><?= $row['quantity']; ?></span></td>
                            <td><?= date('d M Y, h:i A', strtotime($row['last_updated'])); ?></td>
                            <td class="text-center">
                                <a href="deleteInventory.php?id=<?= $row['inventory_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this inventory item?')">
                                   <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No inventory records found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Inventory Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addInventoryLabel">
                    <i class="fas fa-plus-circle me-1"></i> Add Inventory
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="product" class="form-label fw-semibold">Select Product</label>
                        <select name="product" id="product" class="form-control rounded-3" required>
                            <option value="">Select Product</option>
                            <?php
                            $query = $conn->prepare("SELECT * FROM product ORDER BY product_name ASC");
                            $query->execute();
                            $result = $query->get_result();
                            while ($row = $result->fetch_assoc()):
                            ?>
                            <option value="<?= htmlspecialchars($row['product_id']); ?>">
                                <?= htmlspecialchars($row['product_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-semibold">Quantity</label>
                        <input type="number" name="quantity" class="form-control rounded-3" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addInventory" class="btn btn-primary">
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

<script>
document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-product_name');
        const price = button.getAttribute('data-price');
        const description = button.getAttribute('data-description');

        document.getElementById('modal_product_id').value = id;
        document.getElementById('modal_product_name').value = name;
        document.getElementById('modal_price').value = price;
        document.getElementById('modal_description').value = description;
    });
});
</script>


</body>

</html>