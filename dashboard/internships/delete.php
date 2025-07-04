<?php
session_start();

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: /login');
    exit;
}

$company_id = $_SESSION['company_id'];
$level = 3;

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    header('Location: /dashboard/internships');
    exit;
}

// Include database connection settings
include('../../include/dbconn.php');

// Get company information
$company_query = "SELECT * FROM company WHERE company_id = $company_id";
$company_result = mysqli_query($dbconn, $company_query);

if (!$company_result || mysqli_num_rows($company_result) == 0) {
    header('Location: /login');
    exit;
}

$company = mysqli_fetch_assoc($company_result);
$company_name = $company['company_name'];
$logo = $company['logo'];

// Verify that the company owns this vacancy
$verify_query = "SELECT v.vacancy_id, v.title FROM vacancy v 
                    WHERE v.vacancy_id = '$id' AND v.company_id = '$company_id'";
$verify_result = mysqli_query($dbconn, $verify_query);

if (mysqli_num_rows($verify_result) == 0) {
    // Company doesn't own this vacancy
    header('Location: /dashboard/internships?error=unauthorized');
    exit;
}

$vacancy = mysqli_fetch_assoc($verify_result);

// Check if there are any applications for this vacancy
$app_check = "SELECT COUNT(*) as app_count FROM applicant WHERE vacancy_id = '$id'";
$app_result = mysqli_query($dbconn, $app_check);
$app_data = mysqli_fetch_assoc($app_result);
$has_applications = $app_data['app_count'] > 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if ($has_applications) {
        // Cannot delete vacancy with applications, just set status to closed
        $sql = "UPDATE vacancy SET vacancy_status_id = 2 WHERE vacancy_id = '$id'";
        mysqli_query($dbconn, $sql);
        header('Location: /dashboard/internships?info=vacancy_closed');
        exit;
    } else {
        // No applications, safe to delete
        $sql = "DELETE FROM vacancy WHERE vacancy_id = '$id'";
        if (mysqli_query($dbconn, $sql)) {
            header('Location: /dashboard/internships?success=deleted');
            exit;
        } else {
            header('Location: /dashboard/internships?error=delete_failed');
            exit;
        }
    }
}

// For UI consistency
$full_name = $company_name;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Internship - FSKM Internship Portal</title>
    <link rel="stylesheet" href="../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-[100dvh] w-full flex flex-col bg-[var(--secondary)] text-[var(--foreground)]">
        <div class="w-full flex items-center justify-between px-6 py-4 bg-[var(--card)] shadow-sm sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <a href="/dashboard/internships" class="flex items-center gap-2 text-[var(--muted)] hover:text-[var(--primary)]">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Internships</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full overflow-hidden">
                    <?php
                    if ($logo) {
                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                    } else {
                        echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                    }
                    ?>
                </div>
                <span class="text-sm font-medium"><?php echo $full_name; ?></span>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-6">
            <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] shadow-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-medium text-center text-[var(--foreground)] mb-2">
                        <?php echo $has_applications ? 'Close Internship' : 'Delete Internship'; ?>
                    </h3>
                    
                    <p class="text-sm text-[var(--muted)] text-center mb-6">
                        <?php if ($has_applications): ?>
                            This internship has received applications and cannot be permanently deleted. It will be closed instead, preventing new applications while preserving existing ones.
                        <?php else: ?>
                            Are you sure you want to delete "<strong><?php echo $vacancy['title']; ?></strong>"? This action cannot be undone.
                        <?php endif; ?>
                    </p>

                    <div class="flex gap-3">
                        <a href="/dashboard/internships" class="flex-1 px-4 py-2 text-sm font-medium text-[var(--foreground)] bg-[var(--secondary)] border border-[var(--border)] rounded-md hover:bg-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition text-center">
                            Cancel
                        </a>
                        
                        <form method="POST" class="flex-1">
                            <button type="submit" name="confirm_delete" value="1" class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                                <?php echo $has_applications ? 'Close Internship' : 'Delete Permanently'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // If no theme in localStorage, set to 'light'
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', 'light');
            }
            // Set body class to theme
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(localStorage.getItem('theme'));
        })();
    </script>
</body>
</html>
