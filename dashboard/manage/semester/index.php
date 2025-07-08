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
        $profile_picture = $user['profile_picture'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Semesters - Admin Dashboard</title>
    <link rel="stylesheet" href="../../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
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
                                                class="flex items-center justify-between w-full gap-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-[var(--secondary)]">
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
                                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-[var(--secondary)] pl-6">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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

            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                <div class="space-y-6">
                    <div class="flex flex-row items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Manage Semesters</h1>
                            <p class="text-[var(--muted)]">Add, edit, and manage academic semesters for internship programs.</p>
                        </div>
                        <button onclick="openAddModal()" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Add Semester
                        </button>
                    </div>
                    
                    <!-- Success/Error Messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php
                                switch($_GET['success']) {
                                    case 'added':
                                        echo 'Semester added successfully.';
                                        break;
                                    case 'updated':
                                        echo 'Semester updated successfully.';
                                        break;
                                    case 'deleted':
                                        echo 'Semester deleted successfully.';
                                        break;
                                    case 'activated':
                                        echo 'Semester activated successfully.';
                                        break;
                                    case 'deactivated':
                                        echo 'Semester deactivated successfully.';
                                        break;
                                    default:
                                        echo 'Action completed successfully.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php
                                switch($_GET['error']) {
                                    case 'missing_fields':
                                        echo 'Please fill in all required fields.';
                                        break;
                                    case 'invalid_dates':
                                        echo 'End date must be after start date.';
                                        break;
                                    case 'has_vacancies':
                                        echo 'Cannot delete semester: There are vacancies associated with this semester.';
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

                    <?php
                        $semester_sql = "SELECT * FROM semester ORDER BY start_date DESC";
                        $semester_result = mysqli_query($dbconn, $semester_sql);
                    ?>
                    
                    <div class="bg-[var(--background)] border border-[var(--border)] rounded-lg p-4">
                        <!-- Desktop Table View - Hidden on mobile -->
                        <div class="hidden md:block">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[var(--border)]">
                                        <th class="py-2 text-left">Semester Name</th>
                                        <th class="py-2 text-left">Start Date</th>
                                        <th class="py-2 text-left">End Date</th>
                                        <th class="py-2 text-left">Status</th>
                                        <th class="py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        mysqli_data_seek($semester_result, 0); // Reset pointer
                                        while ($row = mysqli_fetch_assoc($semester_result)): 
                                    ?>
                                        <tr class="border-b border-[var(--border)] hover:bg-[var(--secondary)]">
                                            <td class="py-2 font-medium"><?php echo htmlspecialchars($row['semester_name']); ?></td>
                                            <td class="py-2"><?php echo date('d M Y', strtotime($row['start_date'])); ?></td>
                                            <td class="py-2"><?php echo date('d M Y', strtotime($row['end_date'])); ?></td>
                                            <td class="py-2">
                                                <?php if ($row['is_active']): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <i class="fas fa-pause-circle mr-1"></i>
                                                        Inactive
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-2">
                                                <div class="flex gap-2">
                                                    <button onclick="openEditModal(<?php echo $row['semester_id']; ?>, '<?php echo htmlspecialchars($row['semester_name']); ?>', '<?php echo $row['start_date']; ?>', '<?php echo $row['end_date']; ?>', <?php echo $row['is_active']; ?>)" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs inline-flex items-center gap-1">
                                                        <i class="fas fa-edit text-xs"></i>
                                                        Edit
                                                    </button>
                                                    
                                                    <form method="POST" action="../../../process.php" class="inline">
                                                        <input type="hidden" name="toggle_semester_status" value="1">
                                                        <input type="hidden" name="semester_id" value="<?php echo $row['semester_id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                                        <button type="submit" class="px-2 py-1 <?php echo $row['is_active'] ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600'; ?> text-white rounded text-xs inline-flex items-center gap-1">
                                                            <?php if ($row['is_active']): ?>
                                                                <i class="fas fa-pause text-xs"></i>
                                                                Deactivate
                                                            <?php else: ?>
                                                                <i class="fas fa-play text-xs"></i>
                                                                Activate
                                                            <?php endif; ?>
                                                        </button>
                                                    </form>
                                                    
                                                    <?php
                                                        // Check if semester has associated vacancies
                                                        $check_sql = "SELECT COUNT(*) as vacancy_count FROM vacancy WHERE semester_id = " . $row['semester_id'];
                                                        $check_result = mysqli_query($dbconn, $check_sql);
                                                        $check_row = mysqli_fetch_assoc($check_result);
                                                    ?>
                                                    
                                                    <?php if ($check_row['vacancy_count'] > 0): ?>
                                                        <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed inline-flex items-center gap-1" title="Cannot delete - has associated vacancies">
                                                            <i class="fas fa-lock text-xs"></i>
                                                            Delete
                                                        </span>
                                                    <?php else: ?>
                                                        <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this semester? This action cannot be undone.');">
                                                            <input type="hidden" name="delete_semester" value="1">
                                                            <input type="hidden" name="semester_id" value="<?php echo $row['semester_id']; ?>">
                                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs inline-flex items-center gap-1">
                                                                <i class="fas fa-trash text-xs"></i>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Card View - Visible on mobile only -->
                        <div class="md:hidden space-y-4">
                            <?php 
                                mysqli_data_seek($semester_result, 0); // Reset pointer
                                while ($row = mysqli_fetch_assoc($semester_result)): 
                            ?>
                                <div class="bg-[var(--secondary)] border border-[var(--border)] rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-[var(--foreground)] mb-1"><?php echo htmlspecialchars($row['semester_name']); ?></h3>
                                            <div class="text-sm text-[var(--muted)] space-y-1">
                                                <div><span class="font-medium">Start:</span> <?php echo date('d M Y', strtotime($row['start_date'])); ?></div>
                                                <div><span class="font-medium">End:</span> <?php echo date('d M Y', strtotime($row['end_date'])); ?></div>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if ($row['is_active']): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-pause-circle mr-1"></i>
                                                    Inactive
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        <button onclick="openEditModal(<?php echo $row['semester_id']; ?>, '<?php echo htmlspecialchars($row['semester_name']); ?>', '<?php echo $row['start_date']; ?>', '<?php echo $row['end_date']; ?>', <?php echo $row['is_active']; ?>)" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs inline-flex items-center gap-1">
                                            <i class="fas fa-edit text-xs"></i>
                                            Edit
                                        </button>
                                        
                                        <form method="POST" action="../../../process.php" class="inline">
                                            <input type="hidden" name="toggle_semester_status" value="1">
                                            <input type="hidden" name="semester_id" value="<?php echo $row['semester_id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                            <button type="submit" class="px-3 py-1 <?php echo $row['is_active'] ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600'; ?> text-white rounded text-xs inline-flex items-center gap-1">
                                                <?php if ($row['is_active']): ?>
                                                    <i class="fas fa-pause text-xs"></i>
                                                    Deactivate
                                                <?php else: ?>
                                                    <i class="fas fa-play text-xs"></i>
                                                    Activate
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                        
                                        <?php
                                            // Check if semester has associated vacancies
                                            $check_sql = "SELECT COUNT(*) as vacancy_count FROM vacancy WHERE semester_id = " . $row['semester_id'];
                                            $check_result = mysqli_query($dbconn, $check_sql);
                                            $check_row = mysqli_fetch_assoc($check_result);
                                        ?>
                                        
                                        <?php if ($check_row['vacancy_count'] > 0): ?>
                                            <span class="px-3 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed inline-flex items-center gap-1" title="Cannot delete - has associated vacancies">
                                                <i class="fas fa-lock text-xs"></i>
                                                Delete
                                            </span>
                                        <?php else: ?>
                                            <form method="POST" action="../../../process.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this semester? This action cannot be undone.');">
                                                <input type="hidden" name="delete_semester" value="1">
                                                <input type="hidden" name="semester_id" value="<?php echo $row['semester_id']; ?>">
                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs inline-flex items-center gap-1">
                                                    <i class="fas fa-trash text-xs"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Semester Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[var(--background)] rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Add New Semester</h3>
            <form method="POST" action="../../../process.php">
                <input type="hidden" name="add_semester" value="1">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Semester Name</label>
                        <input type="text" name="semester_name" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md" placeholder="e.g., Semester 1 2025/2026">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Start Date</label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">End Date</label>
                        <input type="date" name="end_date" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md">
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" class="mr-2">
                            <span class="text-sm">Set as active semester</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-[var(--foreground)] border border-[var(--border)] rounded-md hover:bg-[var(--secondary)]">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)]">Add Semester</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Semester Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[var(--background)] rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Edit Semester</h3>
            <form method="POST" action="../../../process.php">
                <input type="hidden" name="update_semester" value="1">
                <input type="hidden" name="semester_id" id="edit_semester_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Semester Name</label>
                        <input type="text" name="semester_name" id="edit_semester_name" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Start Date</label>
                        <input type="date" name="start_date" id="edit_start_date" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">End Date</label>
                        <input type="date" name="end_date" id="edit_end_date" required class="w-full px-3 py-2 border border-[var(--border)] rounded-md">
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="mr-2">
                            <span class="text-sm">Set as active semester</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-[var(--foreground)] border border-[var(--border)] rounded-md hover:bg-[var(--secondary)]">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)]">Update Semester</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../js/Dashboard.js"></script>
    <script>
        (function() {
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', 'light');
            }
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(localStorage.getItem('theme'));
        })();

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

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
        }
        function openEditModal(id, name, startDate, endDate, isActive) {
            document.getElementById('edit_semester_id').value = id;
            document.getElementById('edit_semester_name').value = name;
            document.getElementById('edit_start_date').value = startDate;
            document.getElementById('edit_end_date').value = endDate;
            document.getElementById('edit_is_active').checked = isActive == 1;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
        // Close modals when clicking outside
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
</body>
</html>
