<?php
    session_start();
    // Include database connection settings
    include('../../include/dbconn.php');

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
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
        $company_id = $_SESSION['company_id'];
        $level = 3;
        $queryCompany = "SELECT * FROM company WHERE company_id = $company_id";
        $resultCompany = mysqli_query($dbconn, $queryCompany);
        if ($resultCompany && mysqli_num_rows($resultCompany) > 0) {
            $company = mysqli_fetch_assoc($resultCompany);
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
            // For display in UI
            $full_name = $company_name;
        }
    } else {
        header('Location: /login');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../global.css" />
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
                    $level = isset($_SESSION['level'])? $_SESSION['level'] : null;
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fas fa-th-large"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/internships"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
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
                                    <a href="/dashboard/announcement"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
                                        <i class="fa-solid fa-calendar"></i>
                                        <span>Announcement</span>
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

                <div class="border-t border-[var(--border)] p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                <?php
                                    if ($level == 3) {
                                        // Company
                                        if ($logo) {
                                            echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                        } else {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                        }
                                    } else {
                                        // User
                                        if ($profile_picture) {
                                            echo '<img src="../../src/profile_picture/' . htmlspecialchars($profile_picture) . '" alt="User avatar" class="h-full w-full object-cover" />';
                                        } else {
                                            if ($level == 1) {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-full w-full object-cover" />';
                                            } else if ($level == 2) {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-full w-full object-cover" />';
                                            }
                                        }
                                    }
                                ?>
                            </div>
                            <div class="flex flex-col min-w-0">                            <span class="text-sm font-medium truncate max-w-64">
                                <?php
                                    if ($level == 3) {
                                        echo isset($company_name) ? htmlspecialchars($company_name) : 'Company';
                                    } else {
                                        echo isset($full_name) ? htmlspecialchars($full_name) : 'User';
                                    }
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
                        <a href="../../logout.php"
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
                                        if ($level == 3) {
                                            // Company
                                            if ($logo) {
                                                echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                            } else {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                            }
                                        } else {
                                            // User
                                            if ($profile_picture) {
                                                echo '<img src="../../src/profile_picture/' . htmlspecialchars($profile_picture) . '" alt="User avatar" class="h-full w-full object-cover" />';
                                            } else {
                                                if ($level == 1) {
                                                    echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-full w-full object-cover" />';
                                                } else if ($level == 2) {
                                                    echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-full w-full object-cover" />';
                                                }
                                            }
                                        }
                                    ?>
                                </div>
                            </button>

                            <!-- User Profile Dropdown Content -->
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

            <?php
                if ($level == 3) {
                    // Fetch announcements for this company
                    $announcements_query = "SELECT a.* 
                                          FROM announcement a 
                                          WHERE a.company_id = '$company_id' 
                                          ORDER BY a.created_at DESC";
                    $announcements_result = mysqli_query($dbconn, $announcements_query);
            ?>
                <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                    <div class="space-y-6">
                        <div class="flex flex-row items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight">My Announcements</h1>
                                <p class="text-[var(--muted)]">Manage announcements for your applicants.</p>
                            </div>
                            <div>
                                <a href="/dashboard/announcement/create.php"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                    <i class="fas fa-plus"></i> New Announcement
                                </a>
                            </div>
                        </div>

                        <!-- Announcements List -->
                        <div class="space-y-4">
                            <?php
                                if (mysqli_num_rows($announcements_result) > 0) {
                                    while ($announcement = mysqli_fetch_assoc($announcements_result)) {
                                        $announcement_id = $announcement['announcement_id'];
                                        $title = $announcement['announcement_title'];
                                        $content = $announcement['announcement_content'];
                                        $created_at = date('F j, Y - g:i A', strtotime($announcement['created_at']));
                                        $target_type = 'All Students'; // Since table doesn't support targeting, all announcements go to all students
                            ?>
                                <div class="group relative rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm transition-all">
                                    <!-- Dropdown Menu Trigger -->
                                    <div class="inline-block text-left absolute right-7 top-7 cursor-pointer">
                                        <div class="" onclick="toggleDropdown(this)">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </div>
                                        <div class="hidden absolute right-0 mt-2 w-44 rounded-md bg-[var(--background)] shadow-lg z-30 border border-[var(--border)] dropdown-menu">
                                            <div class="py-1">
                                                <a href="/dashboard/announcement/edit.php?id=<?php echo $announcement_id; ?>" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-[var(--secondary)]">
                                                    <i class="fa-solid fa-pen"></i> Edit Announcement
                                                </a>
                                                <a href="/dashboard/announcement/delete.php?id=<?php echo $announcement_id; ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50" onclick="return confirm('Are you sure you want to delete this announcement?')">
                                                    <i class="fa-solid fa-trash"></i> Delete Announcement
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pr-12">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-[var(--foreground)]"><?php echo $title; ?></h3>
                                                <div class="flex items-center gap-4 mt-2 text-sm text-[var(--muted)]">
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <?php echo $created_at; ?>
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-users"></i>
                                                        <?php echo $target_type; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($content): ?>
                                            <div class="mb-4">
                                                <p class="text-[var(--muted)] leading-relaxed"><?php echo nl2br($content); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex items-center justify-between pt-3 border-t border-[var(--border)]">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-bullhorn mr-1"></i>
                                                    Announcement
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="/dashboard/announcement/edit.php?id=<?php echo $announcement_id; ?>" 
                                                   class="text-sm text-[var(--primary)] hover:text-[var(--primary-hover)] font-medium">
                                                    Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                    }
                                } else {
                            ?>
                                <div class="text-center py-12">
                                    <div class="mx-auto w-24 h-24 bg-[var(--secondary)] rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-bullhorn text-3xl text-[var(--muted)]"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-[var(--foreground)] mb-2">No Announcements Yet</h3>
                                    <p class="text-[var(--muted)] mb-6">You haven't created any announcements. Start communicating with your applicants!</p>
                                    <a href="/dashboard/announcement/create.php"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                        <i class="fas fa-plus"></i> Create Your First Announcement
                                    </a>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>

            <?php
                if ($level == 2) {
                    // For students, show announcements they received
                    $student_announcements_query = "SELECT a.*, c.company_name 
                                                   FROM announcement a 
                                                   JOIN company c ON a.company_id = c.company_id 
                                                   WHERE a.user_id = '$user_id' OR a.user_id IS NULL 
                                                   ORDER BY a.created_at DESC";
                    $student_announcements_result = mysqli_query($dbconn, $student_announcements_query);
            ?>
                <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                    <div class="space-y-6">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Announcements</h1>
                            <p class="text-[var(--muted)]">Stay updated with the latest announcements from companies.</p>
                        </div>

                        <!-- Student Announcements List -->
                        <div class="space-y-4">
                            <?php
                                if (mysqli_num_rows($student_announcements_result) > 0) {
                                    while ($announcement = mysqli_fetch_assoc($student_announcements_result)) {
                                        $title = $announcement['announcement_title'];
                                        $content = $announcement['announcement_content'];
                                        $company_name = $announcement['company_name'];
                                        $created_at = date('F j, Y - g:i A', strtotime($announcement['created_at']));
                                        $is_personal = $announcement['user_id'] == $user_id;
                            ?>
                                <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-[var(--foreground)]"><?php echo $title; ?></h3>
                                            <div class="flex items-center gap-4 mt-2 text-sm text-[var(--muted)]">
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-building"></i>
                                                    <?php echo $company_name; ?>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?php echo $created_at; ?>
                                                </span>
                                                <?php if ($is_personal): ?>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-user mr-1"></i>
                                                        Personal
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($content): ?>
                                        <div class="mt-4">
                                            <p class="text-[var(--muted)] leading-relaxed"><?php echo nl2br($content); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php
                                    }
                                } else {
                            ?>
                                <div class="text-center py-12">
                                    <div class="mx-auto w-24 h-24 bg-[var(--secondary)] rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-bell text-3xl text-[var(--muted)]"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-[var(--foreground)] mb-2">No Announcements</h3>
                                    <p class="text-[var(--muted)]">No announcements available at the moment. Check back later!</p>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                }
            ?>
        </div>
    </div>

    <script src="../../js/Dashboard.js"></script>
    <script>
        // Dropdown functionality for announcements
        function toggleDropdown(btn) {
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                if (!menu.contains(btn)) menu.classList.add('hidden');
            });
            // Toggle this dropdown
            var menu = btn.parentElement.querySelector('.dropdown-menu');
            if (menu) menu.classList.toggle('hidden');
        }
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-menu') && !e.target.closest('[onclick="toggleDropdown(this)"]')) {
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    menu.classList.add('hidden');
                });
            }
        });

        // Theme management
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