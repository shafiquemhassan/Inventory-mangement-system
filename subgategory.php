<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include 'conn.php';

// Add Subcategory
if (isset($_POST['addSubgategory'])) {
    $category_id = $_POST['category'];
    $subcategory = trim($_POST['subcategory_name']);

    $stmt = $conn->prepare("SELECT * FROM subcategory WHERE subcategory_name = ?");
    $stmt->bind_param("s", $subcategory);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Subcategory already exists.');</script>";
    } else {
        $stmt1 = $conn->prepare("INSERT INTO subcategory(category_id, subcategory_name) VALUES(?, ?)");
        $stmt1->bind_param("is", $category_id, $subcategory);

        if ($stmt1->execute()) {
            echo "<script>alert('Subcategory added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding subcategory.');</script>";
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
<title>IMS - Subcategory</title>

<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800,900" rel="stylesheet">
<link href="css/sb-admin-2.min.css" rel="stylesheet">
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

<!-- Main Page Content -->
<div class="container-fluid">

  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-primary fw-bold">
          <i class="fas fa-layer-group me-2"></i>Subcategory Management
      </h1>
      <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addSubcategoryModal">
          <i class="fas fa-plus me-1"></i> Add Subcategory
      </button>
  </div>

  <!-- Subcategory Table -->
  <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
          <div class="table-responsive">
              <table class="table table-hover align-middle" id="dataTable">
                  <thead class="table-primary">
                      <tr>
                          <th>#</th>
                          <th>Category</th>
                          <th>Subcategory</th>
                          <th>Created At</th>
                          <th class="text-center">Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php
                      $query = $conn->query("
                          SELECT s.*, c.category_name 
                          FROM subcategory s 
                          LEFT JOIN category c ON s.category_id = c.category_id 
                          ORDER BY s.subcategory_id DESC
                      ");
                      if ($query->num_rows > 0):
                          $count = 1;
                          while ($row = $query->fetch_assoc()):
                      ?>
                      <tr>
                          <td><?= $count++; ?></td>
                          <td><?= htmlspecialchars($row['category_name']); ?></td>
                          <td><?= htmlspecialchars($row['subcategory_name']); ?></td>
                          <td><?= date('d M Y, h:i A', strtotime($row['created_at'] ?? 'now')); ?></td>
                          <td class="text-center">
                              <a href="deleteSubgategory.php?subcategory_id=<?= $row['subcategory_id']; ?>" 
                                 class="btn btn-sm btn-outline-danger"
                                 onclick="return confirm('Delete this subcategory?');">
                                 <i class="fas fa-trash-alt"></i>
                              </a>
                          </td>
                      </tr>
                      <?php endwhile; else: ?>
                      <tr><td colspan="5" class="text-center text-muted py-4">No subcategories found.</td></tr>
                      <?php endif; ?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>

<!-- Add Subcategory Modal -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-labelledby="addSubcategoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addSubcategoryLabel">
          <i class="fas fa-plus-circle me-1"></i> Add New Subcategory
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <form method="post">
        <div class="modal-body px-4 py-3">
          <div class="mb-3">
            <label for="category" class="form-label fw-semibold">Select Category</label>
            <select name="category" id="category" class="form-control rounded-3" required>
              <option value="">-- Select Category --</option>
              <?php
              $query = $conn->query("SELECT * FROM category ORDER BY category_name ASC");
              while ($cat = $query->fetch_assoc()) {
                  echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="subcategory_name" class="form-label fw-semibold">Subcategory Name</label>
            <input type="text" name="subcategory_name" id="subcategory_name" class="form-control rounded-3" 
                   placeholder="Enter subcategory name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="addSubgategory" class="btn btn-primary">
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
      <span>Copyright &copy; IMS 2025</span>
    </div>
  </div>
</footer>

</div>
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
