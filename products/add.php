<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
if (!isLoggedIn()) redirect('../login.php');
// Adding is handled in dashboard.php
redirect('../dashboard.php');
?>
