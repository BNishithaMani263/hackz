<?php
session_start();
require_once 'includes/auth.php';

$auth = new Auth();
$result = $auth->logout();

// Redirect to home page with success message
header('Location: index.php?message=' . urlencode($result['message']));
exit;
?>
