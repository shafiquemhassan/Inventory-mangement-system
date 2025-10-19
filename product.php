<?php
session_start();
include 'conn.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Add Product
if (isset($_POST['addProduct'])) {
    $brand = $_POST['brand'] ?? '';
    $subcategory = $_POST['subcategory'] ?? '';
    $category = $_POST['category'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($brand && $subcategory && $category && $product_name && $price) {
        $stmt = $conn->prepare("INSERT INTO product (brand_id, subcategory_id, category_id, product_name, price, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisds", $brand, $subcategory, $category, $product_name, $price, $description);
        if ($stmt->execute()) {
            echo "<script>alert(' Product added successfully!'); window.location.href='product.php';</script>";
        } else {
            echo "<script>alert(' Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert(' Please fill all required fields.');</script>";
    }
}

// Update Product
if (isset($_POST['product_update'])) {
    $id = $_POST['id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if (!empty($id) && !empty($name) && !empty($price)) {
        $stmt = $conn->prepare("UPDATE product SET product_name = ?, price = ?, description = ? WHERE product_id = ?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);
        if ($stmt->execute()) {
            echo "<script>alert('✅ Product updated successfully'); window.location.href='product.php';</script>";
        } else {
            echo "<script>alert('❌ Error updating product');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('⚠️ All fields are required');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>IMS - Product Management</title>
<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">
<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    .modal-header { background-color: #4e73df; color: white; }
    .form-label { font-weight: 600; }
    .btn-primary { border-radius: 6px; }
    .btn-outline-danger, .btn-outline-success { border-radius: 6px; }
    .table th, .table td { vertical-align: middle; text-align: center; }
</style>
</head>

<body id="page-top">
<div id="wrapper">

<?php include 'slide.php'; ?>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content">

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </ul>
</nav>

<!-- Main Content -->
<div class="container-fluid">

    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold"><i class="fas fa-box-open me-2"></i> Product Management</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addProductModal">
            <i class="fas fa-plus-circle me-1"></i> Add Product
        </button>
    </div>

    <!-- Product Table -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Subcategory</th>
                            <th>Category</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "
                        SELECT 
                            p.product_id, b.brand_name, s.subcategory_name, c.category_name,
                            p.product_name, p.price, p.description, p.created_at
                        FROM product p
                        JOIN brand b ON p.brand_id = b.brand_id
                        JOIN category c ON p.category_id = c.category_id
                        JOIN subcategory s ON p.subcategory_id = s.subcategory_id
                        ORDER BY p.created_at DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['product_id']; ?></td>
                        <td><?= htmlspecialchars($row['brand_name']); ?></td>
                        <td><?= htmlspecialchars($row['subcategory_name']); ?></td>
                        <td><?= htmlspecialchars($row['category_name']); ?></td>
                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                        <td>$<?= number_format($row['price'], 2); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-outline-success btn-sm editBtn"
                                data-id="<?= $row['product_id']; ?>"
                                data-name="<?= htmlspecialchars($row['product_name']); ?>"
                                data-price="<?= $row['price']; ?>"
                                data-desc="<?= htmlspecialchars($row['description']); ?>"
                                data-toggle="modal" data-target="#editModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="deleteProduct.php?id=<?= $row['product_id']; ?>"
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                               <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-muted text-center py-4">No products found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus-circle me-1"></i> Add New Product</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" class="p-3">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Brand</label>
            <select name="brand" class="form-control" required>
              <option value="">Select Brand</option>
              <?php
              $brands = $conn->query("SELECT * FROM brand ORDER BY brand_name ASC");
              while ($b = $brands->fetch_assoc()) {
                  echo '<option value="'.$b['brand_id'].'">'.$b['brand_name'].'</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Subcategory</label>
            <select name="subcategory" class="form-control" required>
              <option value="">Select Subcategory</option>
              <?php
              $subs = $conn->query("SELECT * FROM subcategory ORDER BY subcategory_name ASC");
              while ($s = $subs->fetch_assoc()) {
                  echo '<option value="'.$s['subcategory_id'].'">'.$s['subcategory_name'].'</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-control" required>
              <option value="">Select Category</option>
              <?php
              $cats = $conn->query("SELECT * FROM category ORDER BY category_name ASC");
              while ($c = $cats->fetch_assoc()) {
                  echo '<option value="'.$c['category_id'].'">'.$c['category_name'].'</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="product_name" class="form-control" placeholder="Enter product name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" class="form-control" step="0.01" placeholder="Enter price" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Enter product description"></textarea>
        </div>
        <div class="text-center">
          <button type="submit" name="addProduct" class="btn btn-primary px-4">Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-1"></i> Edit Product</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" class="p-3">
        <input type="hidden" name="id" id="edit_id">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="product_name" id="edit_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" id="edit_price" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" id="edit_desc" class="form-control" rows="3"></textarea>
        </div>
        <div class="text-center">
          <button type="submit" name="product_update" class="btn btn-primary px-4">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>

<footer class="sticky-footer bg-white">
  <div class="container my-auto text-center">
    <span>© IMS Dashboard 2025</span>
  </div>
</footer>

</div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
    $('.editBtn').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_desc').val($(this).data('desc'));
    });
});
</script>
</body>
</html>
