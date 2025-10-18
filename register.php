<?php
include 'conn.php';

if (isset($_POST['Register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['cpassword'];

    if ($password === $confirm_password) {
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Email already exists. Please use a different one.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong during registration.";
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $error = "Passwords do not match.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IMS - Register</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            width: 100%;
            max-width: 950px;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        .card-left {
            background: #f8f9fc;
            flex: 1;
            padding: 3rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .card-left h3 {
            color: #224abe;
            font-weight: 700;
        }

        .card-left p {
            color: #5a5c69;
        }

        .card-right {
            background: #fff;
            flex: 1;
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

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card {
                flex-direction: column;
            }
            .card-left, .card-right {
                width: 100%;
                padding: 2rem;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">
        <div class="card">
            <!-- Left Section -->
            <div class="card-left">
                <i class="fas fa-warehouse fa-3x mb-3 text-primary"></i>
                <h3>Inventory Management System</h3>
                <p>Manage your products, users, and categories efficiently — start your journey today!</p>
            </div>

            <!-- Right Section -->
            <div class="card-right">
                <h4 class="text-center mb-4 text-primary font-weight-bold">Create Your Account 📝</h4>

                <?php if (isset($error)) : ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group mb-3">
                        <label for="username" class="text-muted small">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="text-muted small">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="text-muted small">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="cpassword" class="text-muted small">Confirm Password</label>
                        <input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm password" required>
                    </div>

                    <button type="submit" name="Register" class="btn btn-primary w-100">Register</button>
                </form>

                <div class="login-link">
                    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
