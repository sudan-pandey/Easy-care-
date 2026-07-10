<?php
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusinessBuilder Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero { background: #f8f9fa; padding: 100px 0; text-align: center; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <header class="hero">
        <div class="container">
            <h1>Create your business website in minutes.</h1>
            <p class="lead">No coding. No design skills. Professional results.</p>
            <a href="register.php" class="btn btn-primary btn-lg">Start Building</a>
        </div>
    </header>

    <div class="container my-5">
        <div class="row text-center">
            <div class="col-md-4">
                <h3>Easy Setup</h3>
                <p>Fill out a simple form with your business details.</p>
            </div>
            <div class="col-md-4">
                <h3>Product Showcase</h3>
                <p>Add and manage your products with ease.</p>
            </div>
            <div class="col-md-4">
                <h3>Instant Site</h3>
                <p>Get a live, responsive website immediately.</p>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 border-top">
        <p>&copy; 2025 BusinessBuilder Nepal</p>
    </footer>
</body>
</html>
