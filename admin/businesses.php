<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!isAdmin()) redirect('../login.php');

$businesses = [];
try {
    $businesses = $pdo->query("SELECT b.*, u.name as owner FROM businesses b JOIN users u ON b.user_id = u.id")->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Businesses - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-danger">
        <div class="container"><a class="navbar-brand" href="dashboard.php">Admin Panel</a></div>
    </nav>
    <div class="container mt-4">
        <h3>Businesses</h3>
        <table class="table table-bordered bg-white">
            <thead><tr><th>ID</th><th>Name</th><th>Owner</th><th>Slug</th></tr></thead>
            <tbody>
                <?php foreach ($businesses as $b): ?>
                    <tr><td><?php echo $b['id']; ?></td><td><?php echo htmlspecialchars($b['name']); ?></td><td><?php echo htmlspecialchars($b['owner']); ?></td><td><?php echo htmlspecialchars($b['slug']); ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
