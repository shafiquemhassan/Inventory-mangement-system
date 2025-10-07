
<?php
session_start();


if ( ! isset($_SESSION['username']) ) {
    header('Location: login.php');
    exit;
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
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                <!-- card -->
               <?php include 'card.php'; ?>
               <!-- end of card -->

               <?php
include 'conn.php';


$areaData = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total 
    FROM product 
    GROUP BY month 
    ORDER BY month
");

$months = [];
$totals = [];

while ($row = $areaData->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = $row['total'];
}

$donutData = $conn->query("
    SELECT c.category_name, COUNT(*) AS total 
    FROM product p 
    JOIN category c ON p.category_id = c.category_id 
    GROUP BY c.category_name
");

$categories = [];
$categoryTotals = [];

while ($row = $donutData->fetch_assoc()) {
    $categories[] = $row['category_name'];
    $categoryTotals[] = $row['total'];
}
?>

           <div class="row">
                                
                        <div class="col-xl-8 col-lg-7">

                            <!-- Area Chart -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Product Chart</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                    <hr>
                                  
                                </div>
                            </div>

                        </div>

                                                <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Donut Chart</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <hr>
                                   
                                </div>
                            </div>
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
    
    <?php
// Fetch data from DB
$months = ["Jan", "Feb", "Mar"];  
$totals = [10, 20, 15];           

$categories = ["Brand", "Gategory", "Sub Gategory"];
$categoryTotals = [5, 10, 8];
?>
    
<script>
    const areaLabels = <?php echo json_encode($months); ?>;
    const areaData = <?php echo json_encode($totals); ?>;

    const donutLabels = <?php echo json_encode($categories); ?>;
    const donutData = <?php echo json_encode($categoryTotals); ?>;

    const ctxArea = document.getElementById("myAreaChart").getContext('2d');
    new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: areaLabels,
            datasets: [{
                label: "Products Added",
                data: areaData,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                lineTension: 0.3
            }]
        },
        options: {
            scales: {
                x: { title: { display: true, text: "Month" }},
                y: { beginAtZero: true, title: { display: true, text: "Total Products" }}
            }
        }
    });

    const ctxDonut = document.getElementById("myPieChart").getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>



</body>

</html>