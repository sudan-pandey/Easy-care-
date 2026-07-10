<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) redirect('../login.php');

$user_id = $_SESSION['user_id'];

// Check if user already has a business
$stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ?");
$stmt->execute([$user_id]);
if ($stmt->fetch()) {
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, slug) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $name, $slug]);
    redirect('../dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Business - BusinessBuilder Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Create Your Business</h2>
        <form method="POST">
            <div class="mb-3"><label>Business Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label>Slug (URL name)</label><input type="text" name="slug" class="form-control" required></div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</body>
</html>
