<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Fetch Business
$business = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $business = $stmt->fetch();
} catch (Exception $e) {}

// Fetch Products if business exists
$products = [];
if ($business) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE business_id = ?");
        $stmt->execute([$business['id']]);
        $products = $stmt->fetchAll();
    } catch (Exception $e) {}
}

// Handling Business Info Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_business'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $slug = $_POST['slug'];
    $theme = $_POST['theme'];

    $logo = $business ? $business['logo'] : '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $logo = time() . '_' . $_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'], 'uploads/logos/' . $logo);
    }

    try {
        if ($business) {
            $stmt = $pdo->prepare("UPDATE businesses SET name=?, category=?, description=?, phone=?, email=?, address=?, slug=?, theme=?, logo=? WHERE user_id=?");
            $stmt->execute([$name, $category, $description, $phone, $email, $address, $slug, $theme, $logo, $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, category, description, phone, email, address, slug, theme, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $category, $description, $phone, $email, $address, $slug, $theme, $logo]);
        }
    } catch (Exception $e) {}
    redirect('dashboard.php');
}

// Handling Product Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $p_name = $_POST['p_name'];
    $price = $_POST['price'];
    $p_desc = $_POST['p_desc'];
    $image = '';
    if (isset($_FILES['p_image']) && $_FILES['p_image']['error'] === 0) {
        $image = time() . '_' . $_FILES['p_image']['name'];
        move_uploaded_file($_FILES['p_image']['tmp_name'], 'uploads/products/' . $image);
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO products (business_id, name, price, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$business['id'], $p_name, $price, $p_desc, $image]);
    } catch (Exception $e) {}
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - BusinessBuilder Nepal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'includes/header.php'; ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">Business Information</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Business Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($business['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Category</label>
                                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($business['category'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Slug (URL name)</label>
                                <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($business['slug'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"><?php echo htmlspecialchars($business['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Theme</label>
                                <select name="theme" class="form-select">
                                    <option value="modern" <?php echo ($business['theme']??'') == 'modern' ? 'selected' : ''; ?>>Modern</option>
                                    <option value="classic" <?php echo ($business['theme']??'') == 'classic' ? 'selected' : ''; ?>>Classic</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($business['phone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($business['email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($business['address'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Logo</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                            <button type="submit" name="save_business" class="btn btn-success">Save Business</button>
                            <?php if ($business): ?>
                                <a href="public/business.php?slug=<?php echo htmlspecialchars($business['slug']); ?>" target="_blank" class="btn btn-info">View Site</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <?php if ($business): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header bg-dark text-white">Add Product</div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Product Name</label>
                                    <input type="text" name="p_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Price</label>
                                    <input type="text" name="price" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="p_desc" class="form-control"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Product Image</label>
                                    <input type="file" name="p_image" class="form-control">
                                </div>
                                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">Existing Products</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr><th>Name</th><th>Price</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                                            <td><?php echo htmlspecialchars($p['price']); ?></td>
                                            <td>
                                                <a href="products/delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">Please save your business info first.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
