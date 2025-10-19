<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include 'conn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Inventory Management Dashboard">
    <meta name="author" content="">
    <title>IMS - Dashboard</title>

    <!-- Fonts and Styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .chart-area,
        .chart-pie {
            padding: 1rem;
        }

        .card-header {
            background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
            color: #fff;
            font-weight: 600;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .stat-card {
            border-left: 4px solid #4e73df;
            background-color: #fff;
        }

        .stat-title {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4e73df;
        }
    </style>
</head>

<body id="page-top">

    <div id="wrapper">
        <?php include 'slide.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow-sm">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <a href="logout.php" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </ul>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid">

                    <!-- Dashboard Header -->
                    <div class="dashboard-header mb-4">
                        <h1 class="h3 text-gray-800 font-weight-bold">Dashboard Overview</h1>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row">
                        <?php
                        $totalProducts = $conn->query("SELECT COUNT(*) AS total FROM product")->fetch_assoc()['total'];
                        $totalCategories = $conn->query("SELECT COUNT(*) AS total FROM category")->fetch_assoc()['total'];
                        $totalBrands = $conn->query("SELECT COUNT(*) AS total FROM brand")->fetch_assoc()['total'];
                        $totalInventory = $conn->query("SELECT COUNT(*) AS total FROM inventory")->fetch_assoc()['total'];
                        ?>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card shadow-sm p-3">
                                <div class="stat-title">Total Products</div>
                                <div class="stat-value"><?php echo $totalProducts; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card shadow-sm p-3">
                                <div class="stat-title">Categories</div>
                                <div class="stat-value"><?php echo $totalCategories; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card shadow-sm p-3">
                                <div class="stat-title">Brands</div>
                                <div class="stat-value"><?php echo $totalBrands; ?></div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card shadow-sm p-3">
                                <div class="stat-title">Inventory</div>
                                <div class="stat-value"><?php echo $totalInventory; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header">Product Trends</div>
                                <div class="card-body">
                                    <canvas id="myAreaChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Donut Chart -->
                        <div class="col-xl-4 col-lg-5 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header">Category Distribution</div>
                                <div class="card-body">
                                    <canvas id="myPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <footer class="sticky-footer bg-white shadow-sm">
                <div class="container my-auto text-center">
                    <span class="text-muted">© <?php echo date('Y'); ?> Inventory Management System</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <?php
    $areaData = $conn->query("SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS total FROM product GROUP BY month ORDER BY month");
    $months = $totals = [];
    while ($r = $areaData->fetch_assoc()) {
        $months[] = $r['month'];
        $totals[] = $r['total'];
    }

    $donutData = $conn->query("SELECT c.category_name, COUNT(*) AS total FROM product p JOIN category c ON p.category_id=c.category_id GROUP BY c.category_name");
    $categories = $categoryTotals = [];
    while ($r = $donutData->fetch_assoc()) {
        $categories[] = $r['category_name'];
        $categoryTotals[] = $r['total'];
    }
    ?>

    <script>
        const ctxArea = document.getElementById("myAreaChart").getContext('2d');
        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: "Products Added",
                    data: <?php echo json_encode($totals); ?>,
                    backgroundColor: "rgba(78,115,223,0.1)",
                    borderColor: "rgba(78,115,223,1)",
                    lineTension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const ctxPie = document.getElementById("myPieChart").getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode($categoryTotals); ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverOffset: 6
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>
