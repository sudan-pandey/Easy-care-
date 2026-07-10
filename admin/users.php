<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!isAdmin()) redirect('../login.php');

$users = [];
try {
    $users = $pdo->query("SELECT * FROM users")->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-danger">
        <div class="container"><a class="navbar-brand" href="dashboard.php">Admin Panel</a></div>
    </nav>
    <div class="container mt-4">
        <h3>Users</h3>
        <table class="table table-bordered bg-white">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr><td><?php echo $u['id']; ?></td><td><?php echo htmlspecialchars($u['name']); ?></td><td><?php echo htmlspecialchars($u['email']); ?></td><td><?php echo $u['role']; ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
