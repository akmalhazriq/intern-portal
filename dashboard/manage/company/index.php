<?php
    session_start();
    // Include database connection settings
    include('../../../include/dbconn.php');

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
        if ($level != 1) {
            header('Location: /dashboard');
            exit;
        }
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
    } elseif (isset($_SESSION['company_id'])) {
        // Company should not access admin company management, redirect
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: /login');
        exit;
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
</head>

<body>
    <div class="min-h-[100dvh] flex w-full bg-[var(--secondary)] text-[var(--foreground)]">
        <div class="fixed flex h-screen w-0 flex-col bg-[var(--background)] text-[var(--foreground)] border-r border-[var(--border)] transform transition-all duration-300 ease-in-out overflow-hidden z-20" id="sidebar">
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

        <div class="flex-1 ml-0 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
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
                        <form method="GET" class="mb-4 flex gap-2">
                            <input type="text" name="search" placeholder="Search by company name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="px-3 py-2 border border-[var(--border)] rounded-md w-64" />
                            <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md">Search</button>
                        </form>
                        <?php
                            $search = isset($_GET['search']) ? mysqli_real_escape_string($dbconn, $_GET['search']) : '';
                            $company_sql = "SELECT c.*, u.email, u.full_name, u.level_id FROM company c JOIN user u ON c.user_id = u.user_id";
                            if ($search) {
                                $company_sql .= " WHERE c.company_name LIKE '%$search%'";
                            }
                            $company_sql .= " ORDER BY c.company_name ASC";
                            $company_result = mysqli_query($dbconn, $company_sql);
                        ?>
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
                                            <td class="py-2"><?php echo $row['company_name']; ?></td>
                                            <td class="py-2"><?php echo $row['email']; ?></td>
                                            <td class="py-2">
                                                <?php if (isset($row['verified']) && $row['verified']): ?>
                                                    <span class="text-green-600 font-semibold">Verified</span>
                                                <?php else: ?>
                                                    <span class="text-yellow-600 font-semibold">Unverified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-2 flex gap-2">
                                                <?php if (empty($row['verified'])): ?>
                                                    <form method="POST" action="../../../process.php" class="inline">
                                                        <input type="hidden" name="verify_company" value="1">
                                                        <input type="hidden" name="company_id" value="<?php echo $row['company_id']; ?>">
                                                        <button type="submit" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-xs">Verify</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to unverify this company?');">
                                                        <input type="hidden" name="unverify_company" value="1">
                                                        <input type="hidden" name="company_id" value="<?php echo $row['company_id']; ?>">
                                                        <button type="submit" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Unverify</button>
                                                    </form>
                                                <?php endif; ?>
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
    </script>
</body>
</html>