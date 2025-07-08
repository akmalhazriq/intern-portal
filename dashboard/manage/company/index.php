<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }

    // Include database connection settings
    include('../../../include/dbconn.php');

    // Fetch user data from the database
    $query = "SELECT * FROM user WHERE user_id = $user_id";
    $result = mysqli_query($dbconn, $query);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
        $full_name = $user['full_name'];
        $email = $user['email'];
        $area = $user['area'];
        $state = $user['state'];
        $phone_number = $user['phone_number'];
        $campus = $user['campus'];
        $course = $user['course'];
        $profile_picture = $user['profile_picture'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <style>
        /* Modal backdrop blur effect */
        #companyModal {
            backdrop-filter: blur(4px);
            margin-top: 0 !important;
        }
        
        /* Scrollbar styling for modal content */
        #companyModal .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        
        #companyModal .overflow-y-auto::-webkit-scrollbar-track {
            background: var(--secondary);
        }
        
        #companyModal .overflow-y-auto::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        
        #companyModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: var(--muted);
        }
    </style>
</head>

<body>
    <div class="min-h-[100dvh] flex w-full bg-[var(--secondary)] text-[var(--foreground)]">
        <div class="fixed flex h-screen w-64 flex-col bg-[var(--background)] text-[var(--foreground)] border-r border-[var(--border)] transform transition-all duration-300 ease-in-out overflow-hidden z-20" id="sidebar">
            <div class="p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-briefcase h-6 w-6 text-[var(--primary)] text-xl"></i>
                    <span class="text-lg font-semibold">FSKM Internships</span>
                </div>
            </div>

            <div class="flex flex-col flex-1 overflow-y-auto justify-between">
                <?php
                    if ($level == 1) {
                        
                ?>
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
                            </ul>
                        </div>

                        <div class="px-3 py-2 mt-4">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Manage</h3>
                            <ul class="space-y-1">
                                <li>
                                    <!-- Manage Users Dropdown Parent -->
                                    <div class="relative">
                                        <button type="button" onclick="toggleManageUsersDropdown()" 
                                                class="flex items-center justify-between w-full gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)] hover:bg-[var(--secondary)]">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-users"></i>
                                                <span>Manage Users</span>
                                            </div>
                                            <i class="fas fa-chevron-down transition-transform duration-200" id="manage-users-chevron"></i>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <div id="manage-users-dropdown" class="ml-6 mt-1 space-y-1 hidden">
                                            <a href="/dashboard/manage/users"
                                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-[var(--secondary)] pl-6">
                                                <i class="fa-solid fa-graduation-cap"></i>
                                                <span>Manage Students</span>
                                            </a>
                                            <a href="/dashboard/manage/company"
                                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-[var(--secondary)] pl-6 text-[var(--primary)]">
                                                <i class="fa-solid fa-building"></i>
                                                <span>Manage Companies</span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="/dashboard/manage/vacancy"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fa-solid fa-briefcase"></i>
                                        <span>Manage Vacancy</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/manage/semester"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fa-solid fa-calendar-alt"></i>
                                        <span>Manage Semesters</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/settings"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fas fa-cog"></i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="px-3 py-2 mt-4">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Analytics</h3>
                            <ul class="space-y-1">
                                <li>
                                    <a href="/dashboard/reports"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fa-solid fa-chart-simple"></i>
                                        <span>Reports & Analytics</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php
                    } else if ($level == 2) {
                ?>
                    <div class="flex flex-col">
                        <div class="px-3 py-2">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Main</h3>
                            <ul class="space-y-1">
                                <li>
                                    <a href="/dashboard"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
                                        <i class="fas fa-th-large"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/internships"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Internships</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/status"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>My Applications</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/profile"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fas fa-user"></i>
                                        <span>My Profile</span>
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
                <?php
                    } else if ($level == 3) {
                ?>
                    <div class="flex flex-col">
                        <div class="px-3 py-2">
                            <h3 class="mb-2 text-xs font-semibold uppercase text-[var(--muted)]">Main</h3>
                            <ul class="space-y-1">
                                <li>
                                    <a href="/dashboard"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
                                        <i class="fas fa-th-large"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/internships"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fas fa-briefcase"></i>
                                        <span>My Vacancy</span>
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
                <?php
                    }
                ?>

                <div class="border-t border-t-[var(--border)] p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                <?php
                                    $query = "SELECT * FROM user WHERE user_id = $user_id";
                                    $result = mysqli_query($dbconn, $query);
                                    $user = mysqli_fetch_assoc($result);
                                    $profile_picture = $user['profile_picture'];

                                    if ($profile_picture) {
                                        echo '<img src="../../src/profile_picture/' . $profile_picture . '" alt="User avatar" class="h-full w-full object-cover" />';
                                    } else {
                                        $level = isset($_SESSION['level'])? $_SESSION['level'] : null;
                                        if ($level == 1) {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-full w-full object-cover" />';
                                        } else if ($level == 2) {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-full w-full object-cover" />';
                                        } else if ($level == 3) {
                                            echo '<img src="   https://cdn-icons-png.flaticon.com/512/3061/3061341.png " alt="User avatar" class="h-full w-full object-cover" />';
                                        }
                                    }
                                ?>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
                                    <?php
                                        echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';
                                    ?>
                                </span>
                                <span class="text-xs text-[var(--muted)]">
                                    <?php
                                        if ($level == 1) {
                                            echo 'Admin';
                                        } elseif ($level == 2) {
                                            echo 'Student';
                                        } elseif ($level == 3) {
                                            echo 'Company';
                                        } else {
                                            echo 'Unknown';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <a href="../../../logout.php"
                            class="rounded-md px-4 py-2 hover:bg-[var(--primary)] hover:text-[var(--background)]">
                            <i class="fas fa-sign-out-alt h-4 w-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 ml-64 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
            <header class="bg-[var(--background)] border-b border-[var(--border)] sticky top-0 z-10">
                <div class="flex items-center justify-between px-5 py-2">
                    <div class="flex items-center">
                        <button type="button" id="sidebar-toggle">
                            <i class="fas fa-bars text-xl text-[var(--foreground)]"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Notification Dropdown -->
                        <div class="relative">
                            <!-- Notification Button -->
                            <button type="button" class="relative p-2 rounded-md" id="notification-button">
                            </button>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <!-- User Profile Button -->
                            <button type="button" class="relative p-2 rounded-md hover:bg-[var(--secondary)]" id="user-profile-button">
                                <div class="h-8 w-8 rounded-full overflow-hidden">
                                    <?php
                                        $query = "SELECT * FROM user WHERE user_id = $user_id";
                                        $result = mysqli_query($dbconn, $query);
                                        $user = mysqli_fetch_assoc($result);
                                        $profile_picture = $user['profile_picture'];

                                        if ($profile_picture) {
                                            echo '<img src="../../src/profile_picture/' . $profile_picture . '" alt="User avatar" class="h-full w-full object-cover" />';
                                        } else {
                                            if ($level == 1) {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-full w-full object-cover" />';
                                            } else if ($level == 2) {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-full w-full object-cover" />';
                                            } else if ($level == 3) {
                                                echo '<img src="   https://cdn-icons-png.flaticon.com/512/3061/3061341.png " alt="User avatar" class="h-full w-full object-cover" />';
                                            }
                                        }
                                    ?>
                                </div>
                            </button>

                            <!-- User Profile Dropdown Content -->
                            <div class="hidden absolute right-0 mt-2 w-48 bg-[var(--background)] rounded-md shadow-lg z-50 border border-[var(--border)]" id="user-profile-dropdown">
                                <div class="p-2 border-b border-b-[var(--border)]">
                                    <span class="font-medium">My Account</span>
                                </div>

                                <div class="py-1">
                                    <a href="/dashboard/profile"
                                        class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Profile</a>
                                    <a href="/dashboard/settings"
                                        class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Settings</a>
                                </div>

                                <div class="py-1 border-t border-t-[var(--border)]">
                                    <a href="../../../logout.php" class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <?php
                if ($level == 1) {
            ?>
                <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                    <div class="space-y-6">
                        <div class="flex flex-row items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight">Manage Companies</h1>
                                <p class="text-[var(--muted)]">View and manage company verification status.</p>
                            </div>
                        </div>
                        
                        <!-- Success/Error Messages -->
                        <?php if (isset($_GET['success'])): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                <?php
                                    switch($_GET['success']) {
                                        case 'company_verified':
                                            echo 'Company has been successfully verified.';
                                            break;
                                        case 'company_unverified':
                                            echo 'Company has been successfully unverified.';
                                            break;
                                        case 'company_deleted':
                                            echo 'Company has been successfully deleted.';
                                            break;
                                        default:
                                            echo 'Operation completed successfully.';
                                    }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <?php
                                    switch($_GET['error']) {
                                        case 'unauthorized':
                                            echo 'You are not authorized to perform this action.';
                                            break;
                                        case 'invalid_company':
                                            echo 'Invalid company ID provided.';
                                            break;
                                        case 'company_not_found':
                                            echo 'Company not found.';
                                            break;
                                        case 'operation_failed':
                                            echo 'Operation failed. Please try again.';
                                            break;
                                        default:
                                            echo 'An error occurred. Please try again.';
                                    }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="GET" class="mb-4 flex gap-2">
                            <input type="text" name="search" placeholder="Search by company name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="px-3 py-2 border border-[var(--border)] rounded-md w-64" />
                            <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md">Search</button>
                        </form>
                        <?php
                            $search = '';
                            if (isset($_GET['search'])) {
                                $search = mysqli_real_escape_string($dbconn, $_GET['search']);
                            }
                            
                            // Query only the company table since there's no user_id column
                            $company_sql = "SELECT * FROM company";
                            if ($search) {
                                $company_sql .= " WHERE company_name LIKE '%$search%'";
                            }
                            $company_sql .= " ORDER BY company_name ASC";
                            $company_result = mysqli_query($dbconn, $company_sql);
                        ?>

                        <!-- Company Details Modal -->
                        <div id="companyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="bg-[var(--background)] rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h2 class="text-2xl font-bold">Company Details</h2>
                                            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        <div id="companyDetails"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[var(--background)] border border-[var(--border)] rounded-lg p-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[var(--border)]">
                                        <th class="py-2 text-left">Company Name</th>
                                        <th class="py-2 text-left">Email</th>
                                        <th class="py-2 text-left">Status</th>
                                        <th class="py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($company_result)): ?>
                                        <tr class="border-b border-[var(--border)] hover:bg-[var(--secondary)]">
                                            <td class="py-2"><?php echo htmlspecialchars($row['company_name'] ?? 'N/A'); ?></td>
                                            <td class="py-2"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                                            <td class="py-2">
                                                <?php if (isset($row['verified']) && $row['verified']): ?>
                                                    <span class="text-green-600 font-semibold">Verified</span>
                                                <?php else: ?>
                                                    <span class="text-yellow-600 font-semibold">Unverified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-2">
                                                <div class="flex gap-2">
                                                    <button onclick="viewCompanyDetails(<?php echo $row['company_id']; ?>)" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                                        <i class="fas fa-eye mr-1"></i>View Details
                                                    </button>
                                                    
                                                    <?php if (!$row['verified']): ?>
                                                        <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to verify this company?');">
                                                            <input type="hidden" name="action" value="verify_company">
                                                            <input type="hidden" name="company_id" value="<?php echo $row['company_id']; ?>">
                                                            <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">
                                                                <i class="fas fa-check mr-1"></i>Verify
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to unverify this company? This will disable their account.');">
                                                            <input type="hidden" name="action" value="unverify_company">
                                                            <input type="hidden" name="company_id" value="<?php echo $row['company_id']; ?>">
                                                            <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                                                <i class="fas fa-times mr-1"></i>Unverify
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this company? This will permanently delete all their data including vacancies and applications.');">
                                                        <input type="hidden" name="action" value="delete_company">
                                                        <input type="hidden" name="company_id" value="<?php echo $row['company_id']; ?>">
                                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                                            <i class="fas fa-trash mr-1"></i>Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>
    </div>

    <script src="../../../js/Dashboard.js"></script>
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

        // Function to toggle the Manage Users dropdown
        function toggleManageUsersDropdown() {
            const dropdown = document.getElementById('manage-users-dropdown');
            const chevron = document.getElementById('manage-users-chevron');
            
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                dropdown.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Auto-expand dropdown if we're on a users page
        document.addEventListener('DOMContentLoaded', function() {
            const currentUrl = window.location.href;
            if (currentUrl.includes('/dashboard/manage/users') || currentUrl.includes('/dashboard/manage/company')) {
                const dropdown = document.getElementById('manage-users-dropdown');
                const chevron = document.getElementById('manage-users-chevron');
                if (dropdown && chevron) {
                    dropdown.classList.remove('hidden');
                    chevron.style.transform = 'rotate(180deg)';
                }
            }
        });

        // Company details modal functions
        function viewCompanyDetails(companyId) {
            fetch(`../../../api/get_company_details.php?company_id=${companyId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayCompanyDetails(data.company);
                        document.getElementById('companyModal').classList.remove('hidden');
                    } else {
                        alert('Error loading company details: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading company details');
                });
        }

        function displayCompanyDetails(company) {
            const detailsContainer = document.getElementById('companyDetails');
            const verifyButton = company.verified ? 
                `<button onclick="updateVerificationStatus(${company.company_id}, 0)" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    <i class="fas fa-times-circle mr-1"></i>Unverify Company
                </button>` :
                `<button onclick="updateVerificationStatus(${company.company_id}, 1)" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    <i class="fas fa-check-circle mr-1"></i>Verify Company
                </button>`;

            detailsContainer.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold border-b border-[var(--border)] pb-2">Company Information</h3>
                        <div class="space-y-2">
                            <div><strong>Company Name:</strong> ${company.company_name || 'N/A'}</div>
                            <div><strong>Username:</strong> ${company.username || 'N/A'}</div>
                            <div><strong>Email:</strong> ${company.email || 'N/A'}</div>
                            <div><strong>Phone:</strong> ${company.phone || 'N/A'}</div>
                            <div><strong>Area:</strong> ${company.area || 'N/A'}</div>
                            <div><strong>State:</strong> ${company.state || 'N/A'}</div>
                            <div><strong>Website:</strong> ${company.website ? `<a href="${company.website}" target="_blank" class="text-blue-500 hover:underline">${company.website}</a>` : 'N/A'}</div>
                        </div>
                    </div>

                    <!-- Status and Images -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold border-b border-[var(--border)] pb-2">Status & Media</h3>
                        <div class="space-y-2">
                            <div><strong>Verification Status:</strong> 
                                <span class="${company.verified ? 'text-green-600' : 'text-yellow-600'} font-semibold">
                                    ${company.verified ? 'Verified' : 'Unverified'}
                                </span>
                            </div>
                        </div>
                        
                        ${company.logo ? `
                            <div>
                                <strong>Company Logo:</strong><br>
                                <img src="../../../src/company_logo/${company.logo}" alt="Company Logo" class="mt-2 max-w-32 h-auto border border-[var(--border)] rounded">
                            </div>
                        ` : ''}
                        
                        ${company.banner ? `
                            <div>
                                <strong>Company Banner:</strong><br>
                                <img src="../../../src/company_banner/${company.banner}" alt="Company Banner" class="mt-2 max-w-64 h-auto border border-[var(--border)] rounded">
                            </div>
                        ` : ''}
                    </div>
                </div>

                <!-- Description -->
                ${company.description ? `
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold border-b border-[var(--border)] pb-2">Company Description</h3>
                        <p class="mt-2 text-[var(--muted)]">${company.description}</p>
                    </div>
                ` : ''}

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-3 pt-4 border-t border-[var(--border)]">
                    ${verifyButton}
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        <i class="fas fa-times mr-1"></i>Close
                    </button>
                </div>
            `;
        }

        function updateVerificationStatus(companyId, verified) {
            const action = verified ? 'verify' : 'unverify';
            const confirmMessage = verified ? 
                'Are you sure you want to verify this company?' : 
                'Are you sure you want to unverify this company?';
            
            if (confirm(confirmMessage)) {
                const formData = new FormData();
                formData.append(verified ? 'verify_company' : 'unverify_company', '1');
                formData.append('company_id', companyId);

                fetch('../../../process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Close modal and reload page to reflect changes
                    closeModal();
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(`Error ${action}ing company`);
                });
            }
        }

        function closeModal() {
            document.getElementById('companyModal').classList.add('hidden');
        }

        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('w-64')) {
                        // Hide sidebar
                        sidebar.classList.remove('w-64');
                        sidebar.classList.add('w-0');
                        mainContent.classList.remove('ml-64');
                        mainContent.classList.add('ml-0');
                    } else {
                        // Show sidebar
                        sidebar.classList.remove('w-0');
                        sidebar.classList.add('w-64');
                        mainContent.classList.remove('ml-0');
                        mainContent.classList.add('ml-64');
                    }
                });
            }
        });
    </script>
</body>
</html>