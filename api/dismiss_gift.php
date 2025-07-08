<?php
// Initialize session if not already started
session_start();

// Include database connection settings
include('../include/dbconn.php');
include('../include/security.php');

// Apply security headers
set_security_headers();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Update the user_gift record to mark it as seen
$stmt = $dbconn->prepare("UPDATE user_gift SET seen = 1 WHERE user_id = ? AND seen = 0");
$stmt->bind_param("i", $user_id);
$success = $stmt->execute();

// Unset session variables so alert doesn't show again
unset($_SESSION['show_gift_alert'], $_SESSION['gift_name'], $_SESSION['gift_image'], $_SESSION['gift_id']);

// Return success response
echo json_encode(['success' => $success]);
?>
