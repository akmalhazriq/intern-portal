<?php
// Simple test script to check if everything is working
echo "Testing environment...\n";

// Test database connection
echo "1. Testing database connection... ";
try {
    include './include/dbconn.php';
    echo "✓ Database connected\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test security functions
echo "2. Testing security functions... ";
try {
    include './include/security.php';
    $test_input = sanitize_input("<script>alert('test')</script>");
    echo "✓ Security functions loaded\n";
} catch (Exception $e) {
    echo "✗ Security functions error: " . $e->getMessage() . "\n";
}

// Test session
echo "3. Testing session... ";
try {
    session_start();
    echo "✓ Session started\n";
} catch (Exception $e) {
    echo "✗ Session error: " . $e->getMessage() . "\n";
}

echo "Test completed!\n";
?>
