<?php
session_start();
include('../include/dbconn.php');

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['level']) || $_SESSION['level'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if company_id is provided
if (!isset($_GET['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Company ID is required']);
    exit;
}

$company_id = intval($_GET['company_id']);

// Fetch company details from company table only
$query = "SELECT * FROM company WHERE company_id = ?";
$stmt = mysqli_prepare($dbconn, $query);
mysqli_stmt_bind_param($stmt, "i", $company_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($company = mysqli_fetch_assoc($result)) {
    // Convert numeric verified field to boolean for easier frontend handling
    $company['verified'] = (bool)($company['verified'] ?? 0);
    
    echo json_encode([
        'success' => true,
        'company' => $company
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Company not found'
    ]);
}

mysqli_close($dbconn);
?>
