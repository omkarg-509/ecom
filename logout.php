<?php
require_once 'config/database.php';

// Destroy session and redirect to homepage
session_destroy();
header('Location: index.php');
exit();
?>