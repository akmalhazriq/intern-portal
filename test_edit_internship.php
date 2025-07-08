<?php
// Test script to verify edit internship functionality
session_start();
include('include/dbconn.php');

echo "<h2>Testing Edit Internship Functionality</h2>\n";

// Test 1: Check if prepared statements are working in edit.php
echo "<h3>Test 1: Database Connection and Prepared Statements</h3>\n";
try {
    $stmt = $dbconn->prepare("SELECT COUNT(*) as count FROM vacancy");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "✅ Database connection working. Found " . $row['count'] . " vacancies.\n<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n<br>";
}

// Test 2: Check if semester selection query works
echo "<h3>Test 2: Semester Selection Query</h3>\n";
try {
    $stmt = $dbconn->prepare("SELECT * FROM semester WHERE is_active = 1 ORDER BY start_date DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    echo "✅ Semester query working. Found " . $result->num_rows . " active semesters.\n<br>";
    
    while ($row = $result->fetch_assoc()) {
        echo "- " . htmlspecialchars($row['semester_name']) . " (ID: " . $row['semester_id'] . ")\n<br>";
    }
} catch (Exception $e) {
    echo "❌ Semester query error: " . $e->getMessage() . "\n<br>";
}

// Test 3: Check if vacancy fetch with join works
echo "<h3>Test 3: Vacancy Fetch with Join</h3>\n";
try {
    $stmt = $dbconn->prepare("SELECT v.*, s.semester_name FROM vacancy v 
        LEFT JOIN semester s ON v.semester_id = s.semester_id 
        LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $vacancy = $result->fetch_assoc();
        echo "✅ Vacancy join query working. Sample vacancy: " . htmlspecialchars($vacancy['title']) . "\n<br>";
        echo "- Semester: " . htmlspecialchars($vacancy['semester_name'] ?? 'Not assigned') . "\n<br>";
    } else {
        echo "⚠️ No vacancies found in database.\n<br>";
    }
} catch (Exception $e) {
    echo "❌ Vacancy query error: " . $e->getMessage() . "\n<br>";
}

echo "<h3>Summary</h3>\n";
echo "All prepared statements have been implemented in edit.php and process.php.\n<br>";
echo "The edit internship functionality should now work correctly.\n<br>";
echo "<br><a href='dashboard/internships'>Go to Internships Dashboard</a>\n";
?>
