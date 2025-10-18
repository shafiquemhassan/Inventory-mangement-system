<?php
include 'conn.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No user found with that email.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IMS - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 900px;
        }

        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-left {
            background: #f8f9fc;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .card-left h3 {
            font-weight: 700;
            color: #224abe;
        }

        .card-left p {
            color: #5a5c69;
            margin-bottom: 1.5rem;
        }

        .card-right {
            background: #fff;
            padding: 3rem;
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 0.85rem 1rem;
        }

        .btn-primary {
            background: linear-gradient(90deg, #4e73df, #224abe);
            border: none;
            border-radius: 0.75rem;
            padding: 0.8rem;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #224abe, #1d3fbf);
            transform: translateY(-2px);
        }

        .error-message {
            color: #dc3545;
            font-weight: 600;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
        }

        .register-link a {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card {
                flex-direction: column;
            }

            .card-left,
            .card-right {
                width: 100%;
                padding: 2rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="card d-flex flex-row">
            <!-- Left Panel -->
            <div class="card-left col-lg-6">
                <i class="fas fa-boxes fa-3x mb-3 text-primary"></i>
                <h3>Inventory Management System</h3>
                <p>Organize your products, track categories, and manage users efficiently.</p>
            </div>

            <!-- Right Panel -->
            <div class="card-right col-lg-6">
                <h4 class="text-center mb-4 text-primary font-weight-bold">Welcome Back 👋</h4>

                <?php if (isset($error)) : ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group mb-3">
                        <label for="email" class="text-muted small">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="password" class="text-muted small">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>

                <div class="register-link">
                    <p class="mt-3">Don't have an account? <a href="register.php">Create one</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
