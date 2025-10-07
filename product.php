
<?php
session_start();

include 'conn.php';

if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
}

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
            echo "<script>alert('Product added successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill all required fields.');</script>";
    }
}



if (isset($_POST['product_update'])) {
    $id = $_POST['id'];
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    if (!empty($id) && !empty($name) && !empty($price)) {
        $stmt = $conn->prepare("UPDATE product SET product_name = ?, price = ?, description = ? WHERE product_id = ?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully'); window.location.href = 'product.php';</script>";
        } else {
            echo "<script>alert('Error updating product');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required');</script>";
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Product</h1>
                    </div>
<!-- model -->

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Add
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
            <div class="row">
                <div class="col-4">
                            <div class="my-3">
            <label for="brand" class="form-label">Brand</label>
            <select name="brand" id="brand" class="form-control">
                <option value="">Select Brand</option>
                <?php
                $query = $conn->prepare("SELECT * FROM brand");
                $query->execute();
                $result = $query->get_result();

                while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo htmlspecialchars($row['brand_id']); ?>">
                        <?php echo htmlspecialchars($row['brand_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
                </div>

                <div class="col-4">
       <div class="my-3">
            <label for="brand" class="form-label">Sub Gategory</label>   
         <select name="subcategory" id="subgategory" class="form-control">

                <option value="">Select Brand</option>
                <?php
                $query = $conn->prepare("SELECT * FROM subcategory");
                $query->execute();
                $result = $query->get_result();

                while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo htmlspecialchars($row['subcategory_id']); ?>">
                        <?php echo htmlspecialchars($row['subcategory_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
                </div>

                <div class="col-4">
 <div class="my-3">
         <label for="gategory" class="form-label">Gategory</label>
            <select name="category" id="category" class="form-control">
                <option value="">Select Category</option>
                <?php
                $query = $conn->prepare("SELECT * FROM category");
                $query->execute();
                $result = $query->get_result();

                while ($row = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo htmlspecialchars($row['category_id']); ?>">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
                </div>
            </div>

            <div class="my-3">
                <label for="" class="form-label">Product Name</label>
                <input type="text" placeholder="Please Enter Product Name" class="form-control" name="product_name" >
            </div>

            <div class="my-3">
                <label for="" class="form-label">Price</label>
                <input type="number" placeholder="Please Enter Price" name="price" class="form-control">
            </div>

            <div class="my-3">
  <label for="description" class="form-label">Description</label>
  <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter product description..."></textarea>
</div>
<div class="d-flex justify-content-center my-3">
<button class="btn btn-primary" name="addProduct">Add</button>
</div>

        </form>
      </div>
    </div>
  </div>
</div>
                    <!-- table -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Brand</th>
                                            <th>Sub Gategory</th>
                                            <th>Gategory</th>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Description</th>
                                            <th>CREATED_At</th>
                                            <th>Edit</th>
                                            <th>Del</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                       <?php
$sql = <<<SQL
SELECT 
    p.product_id AS product_id,
    b.brand_name AS brand_name,
    s.subcategory_name AS subcategory_name,
    c.category_name AS category_name,
    p.product_name,
    p.price,
    p.description,
    p.created_at
FROM product p
JOIN brand b ON p.brand_id = b.brand_id
JOIN category c ON p.category_id = c.category_id
JOIN subcategory s ON p.subcategory_id = s.subcategory_id
ORDER BY p.created_at DESC
SQL;

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    ?>
    <tr>
        <td><?php echo $row['product_id']; ?></td>
        <td><?php echo $row['brand_name']; ?></td>
        <td><?php echo $row['subcategory_name']; ?></td>
        <td><?php echo $row['category_name']; ?></td>
        <td><?php echo $row['product_name']; ?></td>
        <td><?php echo $row['price']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
            <a href="deleteProduct.php?id=<?php echo $row['product_id']; ?>" 
   class="btn btn-danger" 
   onclick="return confirm('Are you sure you want to delete this product?');">DEL</a>

        </td>
        <td>
            <a href="#" class="btn btn-success editBtn" data-toggle="modal" data-target="#exampleModal1"
                data-id="<?php echo $row['product_id']; ?>"
                data-product_name="<?php echo htmlspecialchars($row['product_name']); ?>"
                data-price="<?php echo $row['price']; ?>"
                data-description="<?php echo htmlspecialchars($row['description']); ?>">
                Edit
            </a>
        </td>
    </tr>
<?php } ?>
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-1" id="exampleModalLabel">Update Product</h1>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>


                        </div>
                        <div class="modal-body">
                            <form action="" method="POST">

                                <input type="hidden" name="id" id="modal_product_id" value="">

                                <div class="my-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="modal_product_name" name="product_name">
                                </div>

                                <div class="my-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="modal_price" name="price">
                                </div>

                                <div class="my-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="modal_description" name="description">

                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary" name="product_update">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                                    </tbody>
                                </table>
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