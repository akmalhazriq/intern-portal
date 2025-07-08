<?php
session_start();

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header('Location: /login');
    exit;
}

$company_id = $_SESSION['company_id'];
$level = 3;

// Include database connection settings
include('../../include/dbconn.php');
include('../../include/security.php');

// Set security headers
set_security_headers();

// Get company information
$company_query = "SELECT * FROM company WHERE company_id = ?";
$stmt = mysqli_prepare($dbconn, $company_query);
mysqli_stmt_bind_param($stmt, "i", $company_id);
mysqli_stmt_execute($stmt);
$company_result = mysqli_stmt_get_result($stmt);

if (!$company_result || mysqli_num_rows($company_result) == 0) {
    header('Location: /login');
    exit;
}

$company = mysqli_fetch_assoc($company_result);
$company_name = $company['company_name'];
$phone = $company['phone'];
$email_company = $company['email'];
$area_company = $company['area'];
$state_company = $company['state'];
$description = $company['description'];
$website = $company['website'];
$verified = $company['verified'];
$banner = $company['banner'];
$logo = $company['logo'];

// For UI consistency
$full_name = $company_name;
$level = 3;

$error = '';
$success = '';

// Handle URL parameters for messages
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'required_fields':
            $error = 'Please fill in all required fields.';
            break;
        case 'create_failed':
            $error = 'Failed to create vacancy. Please try again.';
            break;
        default:
            $error = 'An error occurred. Please try again.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Internship - FSKM Internship Portal</title>
    <link rel="stylesheet" href="../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com" 
            integrity="sha384-..." 
            crossorigin="anonymous"></script>
</head>

<body>
    <div class="min-h-[100dvh] flex w-full bg-[var(--secondary)] text-[var(--foreground)]">
        <!-- Sidebar -->
        <div class="fixed flex h-screen w-0 flex-col bg-[var(--background)] text-[var(--foreground)] border-r border-[var(--border)] transform transition-all duration-300 ease-in-out overflow-hidden z-20" id="sidebar">
            <div class="p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-briefcase h-6 w-6 text-[var(--primary)] text-xl"></i>
                    <span class="text-lg font-semibold">FSKM Internships</span>
                </div>
            </div>

            <div class="flex flex-col flex-1 overflow-y-auto justify-between">
                <div class="flex flex-col">
                    <div class="px-3 py-2">
                        <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Main</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="/dashboard"
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-th-large"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/internships"
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
                                    <i class="fas fa-briefcase"></i>
                                    <span>My Internships</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/applications"
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                    <i class="fa-solid fa-clipboard"></i>
                                    <span>Applications</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/profile"
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-user"></i>
                                    <span>Company Profile</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="px-3 py-2 mt-4">
                        <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Manage</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="/dashboard/settings"
                                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-[var(--border)] p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                <?php
                                if ($logo) {
                                    echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                } else {
                                    echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                }
                                ?>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
                                    <?php echo htmlspecialchars($company_name); ?>
                                </span>
                                <span class="text-xs text-[var(--muted)]">Company</span>
                            </div>
                        </div>
                        <a href="../../logout.php"
                            class="rounded-md px-4 py-2 hover:bg-[var(--primary)] hover:text-[var(--background)]">
                            <i class="fas fa-sign-out-alt h-4 w-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-0 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
            <!-- Header -->
            <header class="bg-[var(--background)] border-b border-[var(--border)] sticky top-0 z-10">
                <div class="flex items-center justify-between px-5 py-2">
                    <div class="flex items-center">
                        <button type="button" id="sidebar-toggle">
                            <i class="fas fa-bars text-xl text-[var(--foreground)]"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button type="button" class="relative p-2 rounded-md hover:bg-[var(--secondary)]" id="user-profile-button">
                                <div class="h-8 w-8 rounded-full overflow-hidden">
                                    <?php
                                    if ($logo) {
                                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
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
                                            <span class="ml-1 text-sm font-medium text-[var(--foreground)] md:ml-2">Create New</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="text-3xl font-bold tracking-tight">Create New Internship</h1>
                            <p class="text-[var(--muted)]">Post a new internship opportunity for <?php echo $company['company_name']; ?>.</p>
                        </div>
                        <div class="flex gap-3">
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

                    <!-- Create Form -->
                    <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] shadow-sm">
                        <div class="p-6">
                            <form method="POST" action="../../process.php" class="space-y-6">
                                <input type="hidden" name="create_internship" value="1">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Title -->
                                    <div class="md:col-span-2">
                                        <label for="title" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Job Title <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="title" 
                                               id="title" 
                                               required
                                               class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]"
                                               placeholder="e.g., Software Developer Intern">
                                    </div>

                                    <!-- Role -->
                                    <div>
                                        <label for="role" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Role/Department <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="role" 
                                               id="role" 
                                               required
                                               class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]"
                                               placeholder="e.g., Information Technology">
                                    </div>

                                    <!-- Allowance -->
                                    <div>
                                        <label for="elaun" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Monthly Allowance (RM)
                                        </label>
                                        <input type="text" 
                                               name="elaun" 
                                               id="elaun" 
                                               class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]"
                                               placeholder="e.g., 800">
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
                                                $semester_query = "SELECT * FROM semester WHERE is_active = 1 ORDER BY start_date DESC";
                                                $semester_result = mysqli_query($dbconn, $semester_query);
                                                if ($semester_result && mysqli_num_rows($semester_result) > 0) {
                                                    while ($semester_row = mysqli_fetch_assoc($semester_result)) {
                                                        $semester_id = (int)$semester_row['semester_id'];
                                                        $semester_name = htmlspecialchars($semester_row['semester_name'], ENT_QUOTES, 'UTF-8');
                                                        $start_date = htmlspecialchars(date('M Y', strtotime($semester_row['start_date'])), ENT_QUOTES, 'UTF-8');
                                                        $end_date = htmlspecialchars(date('M Y', strtotime($semester_row['end_date'])), ENT_QUOTES, 'UTF-8');
                                                        echo '<option value="' . $semester_id . '">' . 
                                                             $semester_name . 
                                                             ' (' . $start_date . ' - ' . $end_date . ')</option>';
                                                    }
                                                } else {
                                                    echo '<option value="" disabled>No active semesters available</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Status -->
                                    <div class="md:col-span-2">
                                        <label for="vacancy_status_id" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Vacancy Status
                                        </label>
                                        <select name="vacancy_status_id" 
                                                id="vacancy_status_id"
                                                class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]">
                                            <option value="1" selected>Open - Accepting Applications</option>
                                            <option value="2">Closed - No Longer Accepting Applications</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-[var(--foreground)] mb-2">
                                            Job Description <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="description" 
                                                  id="description" 
                                                  rows="8" 
                                                  required
                                                  class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent bg-[var(--background)] text-[var(--foreground)]"
                                                  placeholder="Describe the internship position, responsibilities, requirements, and benefits..."></textarea>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border)]">
                                    <a href="/dashboard/internships"
                                        class="px-4 py-2 text-sm font-medium text-[var(--foreground)] bg-[var(--secondary)] border border-[var(--border)] rounded-md hover:bg-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-[var(--primary)] border border-transparent rounded-md hover:bg-[var(--primary-hover)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Internship
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
