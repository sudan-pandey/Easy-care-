<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if (!isLoggedIn()) redirect('../login.php');
// Redirect to public business view
$stmt = $pdo->prepare("SELECT slug FROM businesses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$b = $stmt->fetch();
if ($b) redirect('../public/business.php?slug='.$b['slug']);
redirect('../dashboard.php');
?>
