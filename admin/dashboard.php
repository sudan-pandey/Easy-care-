<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!isAdmin()) redirect('../login.php');

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_users = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM businesses");
$total_businesses = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BusinessBuilder Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Admin Panel</a>
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">Stats</a>
                    <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
                    <a href="businesses.php" class="list-group-item list-group-item-action">Manage Businesses</a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body text-center">
                                <h3><?php echo $total_users; ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body text-center">
                                <h3><?php echo $total_businesses; ?></h3>
                                <p>Total Businesses</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
