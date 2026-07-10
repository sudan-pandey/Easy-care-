<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if (!isLoggedIn()) redirect('../login.php');
// Simple redirect back
redirect('../dashboard.php');
?>
