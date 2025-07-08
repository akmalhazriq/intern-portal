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
$stmt = $dbconn->prepare("SELECT * FROM company WHERE company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$company_result = $stmt->get_result();

if (!$company_result || $company_result->num_rows == 0) {
    header('Location: /login');
    exit;
}

$company = mysqli_fetch_assoc($company_result);
$company_name = $company['company_name'];
$phone = $company['phone'];
$email_company = $company['email'];
$area_company = $company['area'];
$state_company = $company['state'];
$description_company = $company['description'];
$website = $company['website'];
$verified = $company['verified'];
$banner = $company['banner'];
$logo = $company['logo'];

// Fetch vacancy details and verify ownership
$stmt = $dbconn->prepare("SELECT v.*, s.semester_name FROM vacancy v 
    LEFT JOIN semester s ON v.semester_id = s.semester_id
    WHERE v.vacancy_id = ? AND v.company_id = ?");
$stmt->bind_param("ii", $id, $company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $vacancy = $result->fetch_assoc();
    $title = $vacancy['title'];
    $role = $vacancy['role'];
    $elaun = $vacancy['elaun'];
    $description = $vacancy['description'];
    $vacancy_status_id = $vacancy['vacancy_status_id'];
    $current_semester_id = $vacancy['semester_id'];
    $current_semester_name = $vacancy['semester_name'];
} else {
    // Vacancy not found or company doesn't own it
    header('Location: /dashboard/internships');
    exit;
}

$error = '';
$success = '';

// Handle URL parameters for messages
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'required_fields':
            $error = 'Please fill in all required fields.';
            break;
        case 'update_failed':
            $error = 'Failed to update vacancy. Please try again.';
            break;
        case 'unauthorized':
            $error = 'You are not authorized to edit this vacancy.';
            break;
        default:
            $error = 'An error occurred. Please try again.';
    }
}

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'updated':
            $success = 'Vacancy updated successfully!';
            break;
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
    <title>Edit Internship - FSKM Internship Portal</title>
    <link rel="stylesheet" href="../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-[100dvh] flex w-full bg-[var(--secondary)] text-[var(--foreground)]">
        <!-- Main Content -->
        <div class="flex-1 ml-0 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
            <!-- Header -->
            <header class="bg-[var(--background)] border-b border-[var(--border)] sticky top-0 z-10">
                <div class="flex items-center justify-between px-5 py-2">
                    <div class="flex items-center">
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button type="button" class="relative p-2 rounded-md hover:bg-[var(--secondary)]" id="user-profile-button">
                                <div class="h-8 w-8 rounded-full overflow-hidden">
                                    <?php
                                    if ($logo) {
                                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" />';
                                    } else {
                                        echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                    }
                                    ?>
                                </div>
                            </button>

                            <div class="hidden absolute right-0 mt-2 w-48 bg-[var(--background)] rounded-md shadow-lg z-50 border border-[var(--border)]" id="user-profile-dropdown">
                                <div class="p-2 border-b border-[var(--border)]">
                                    <span class="font-medium">My Account</span>
                                </div>

                                <div class="py-1">
                                    <a href="/dashboard/profile"
                                        class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Profile</a>
                                    <a href="/dashboard/settings"
                                        class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Settings</a>
                                </div>

                                <div class="py-1 border-t border-[var(--border)]">
                                    <a href="../../logout.php" class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                <div class="space-y-6">
                    <!-- Breadcrumb and Title -->
                    <div class="flex items-center justify-between">
                        <div>
                            <nav class="flex mb-2" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                    <li class="inline-flex items-center">
                                        <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-[var(--muted)] hover:text-[var(--primary)]">
                                            <i class="fas fa-th-large w-3 h-3 mr-2.5"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <i class="fas fa-chevron-right w-3 h-3 text-[var(--muted)] mx-1"></i>
                                            <a href="/dashboard/internships" class="ml-1 text-sm font-medium text-[var(--muted)] hover:text-[var(--primary)] md:ml-2">Internships</a>
                                        </div>
                                    </li>
                                    <li aria-current="page">
                                        <div class="flex items-center">
                                            <i class="fas fa-chevron-right w-3 h-3 text-[var(--muted)] mx-1"></i>
                                            <span class="ml-1 text-sm font-medium text-[var(--foreground)] md:ml-2">Edit</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="text-3xl font-bold tracking-tight">Edit Internship</h1>
                            <p class="text-[var(--muted)]">Update your internship posting details.</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="/dashboard/internships/view.php?id=<?php echo $id; ?>"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--secondary)] text-[var(--foreground)] rounded-md hover:bg-[var(--muted)] transition">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="/dashboard/internships"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--muted)] text-[var(--background)] rounded-md hover:bg-[var(--primary)] transition">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Alert Messages -->
                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800"><?php echo $error; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800"><?php echo $success; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>                    <!-- Edit Form -->
                    <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] shadow-sm">
                        <div class="p-6">
                            <form method="POST" action="../../process.php" class="space-y-6">
                                <input type="hidden" name="update_internship" value="1">
                                <input type="hidden" name="vacancy_id" value="<?php echo $id; ?>">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Title -->
                                    <div class="md:col-span-2">
                                        <label for="title" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Job Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="title" id="title" value="<?php echo $title; ?>" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]" placeholder="e.g., Software Developer Intern">
                                    </div>

                                    <!-- Role -->
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Role/Department <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                                name="role" 
                                                id="role" 
                                                value="<?php echo $role; ?>"
                                                required
                                                class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]"
                                                placeholder="e.g., Information Technology">
                                    </div>

                                    <!-- Allowance -->
                                    <div>
                                        <label for="elaun" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Monthly Allowance (RM)
                                        </label>
                                        <input type="text" name="elaun" id="elaun" value="<?php echo $elaun; ?>" class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]" placeholder="e.g., 800">
                                    </div>

                                    <!-- Semester -->
                                    <div>
                                        <label for="semester_id" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Semester <span class="text-red-500">*</span>
                                        </label>
                                        <select name="semester_id" 
                                                id="semester_id"
                                                required
                                                class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]">
                                            <option value="">Select a semester</option>
                                            <?php
                                                $stmt = $dbconn->prepare("SELECT * FROM semester WHERE is_active = 1 ORDER BY start_date DESC");
                                                $stmt->execute();
                                                $semester_result = $stmt->get_result();
                                                if ($semester_result && $semester_result->num_rows > 0) {
                                                    while ($semester_row = $semester_result->fetch_assoc()) {
                                                        $selected = ($current_semester_id == $semester_row['semester_id']) ? 'selected' : '';
                                                        echo '<option value="' . $semester_row['semester_id'] . '" ' . $selected . '>' . 
                                                             htmlspecialchars($semester_row['semester_name'] ?? '') . 
                                                             ' (' . date('M Y', strtotime($semester_row['start_date'])) . ' - ' . 
                                                             date('M Y', strtotime($semester_row['end_date'])) . ')</option>';
                                                    }
                                                } else {
                                                    echo '<option value="" disabled>No active semesters available</option>';
                                                }
                                            ?>
                                        </select>
                                        <?php if ($current_semester_name): ?>
                                            <p class="text-xs text-[var(--muted)] mt-1">
                                                Current: <?php echo htmlspecialchars($current_semester_name ?? ''); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Status -->
                                    <div class="md:col-span-2">
                                        <label for="vacancy_status_id" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Vacancy Status
                                        </label>
                                        <select name="vacancy_status_id" id="vacancy_status_id" class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]">
                                            <option value="1" <?php echo ($vacancy_status_id == 1) ? 'selected' : ''; ?>>Open - Accepting Applications</option>
                                            <option value="2" <?php echo ($vacancy_status_id == 2) ? 'selected' : ''; ?>>Closed - No Longer Accepting Applications</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Job Description <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="description" id="description" rows="8" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]" placeholder="Describe the internship position, responsibilities, requirements, and benefits..."><?php echo $description; ?></textarea>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border)]">
                                    <a href="/dashboard/internships" class="px-4 py-2 text-sm font-medium text-[var(--foreground)] bg-[var(--secondary)] border border-[var(--border)] rounded-md hover:bg-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition">
                                        Cancel
                                    </a>
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--primary)] border border-transparent rounded-md hover:bg-[var(--primary-hover)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition">
                                        <i class="fas fa-save mr-2"></i>
                                        Update Internship
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/Dashboard.js"></script>
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

        // Profile dropdown functionality
        document.getElementById('user-profile-button').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('user-profile-dropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-profile-dropdown');
            const button = document.getElementById('user-profile-button');
            if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
