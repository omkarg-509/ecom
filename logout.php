<?php
require_once 'config/session.php';

// Clear all session data
session_destroy();

// Redirect to home page with success message
header("Location: index.php?message=You have been logged out successfully");
exit();
?>