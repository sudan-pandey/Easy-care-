<?php
require_once '../includes/db.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) die("Business not found.");

try {
    $stmt = $pdo->prepare("SELECT * FROM businesses WHERE slug = ?");
    $stmt->execute([$slug]);
    $business = $stmt->fetch();

    if (!$business) die("Business not found.");

    $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = ?");
    $stmt->execute([$business['id']]);
    $products = $stmt->fetchAll();

    $theme = $business['theme'] ?? 'modern';
} catch (Exception $e) {
    die("Database error.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        <?php if ($theme === 'classic'): ?>
            body { font-family: 'Georgia', serif; }
            .hero { background: #5d4037; color: white; padding: 60px 0; }
        <?php else: ?>
            .hero { background: #f8f9fa; padding: 60px 0; }
        <?php endif; ?>
        .product-card img { height: 200px; object-fit: cover; }
    </style>
</head>
<body>
    <header class="hero text-center border-bottom">
        <div class="container">
            <?php if ($business['logo']): ?>
                <img src="../uploads/logos/<?php echo htmlspecialchars($business['logo']); ?>" height="80" class="mb-3">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($business['name']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($business['category']); ?></p>
        </div>
    </header>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h2>Our Products</h2>
            </div>
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($p['image']): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5><?php echo htmlspecialchars($p['name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($p['description']); ?></p>
                            <h6 class="text-primary"><?php echo htmlspecialchars($p['price']); ?></h6>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-light py-5 border-top">
        <div class="container text-center">
            <h2>About Us</h2>
            <p><?php echo nl2br(htmlspecialchars($business['description'])); ?></p>
        </div>
    </div>

    <footer class="container my-5 text-center">
        <div class="row">
            <div class="col-md-4"><h5>Phone</h5><p><?php echo htmlspecialchars($business['phone']); ?></p></div>
            <div class="col-md-4"><h5>Email</h5><p><?php echo htmlspecialchars($business['email']); ?></p></div>
            <div class="col-md-4"><h5>Address</h5><p><?php echo htmlspecialchars($business['address']); ?></p></div>
        </div>
        <hr>
        <p>&copy; 2025 <?php echo htmlspecialchars($business['name']); ?> | Powered by BusinessBuilder Nepal</p>
    </footer>
</body>
</html>
