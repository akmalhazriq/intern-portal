<?php
session_start();
include('../include/dbconn.php');

if (isset($_SESSION['user_id'])) {
    // Student/Admin
    $user_id = $_SESSION['user_id'];
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
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
} elseif (isset($_SESSION['company_id'])) {
    // Company
    $company_id = $_SESSION['company_id'];
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
    }
    $level = 3;
} else {
    // Not logged in
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
    <link rel="stylesheet" href="../global.css" />
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
                    $level = isset($_SESSION['level'])? $_SESSION['level'] : null;
                    if ($level == 1) {
                        
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
                                    <a href="/dashboard/announcement"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
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

                <div class="border-t border-t-[var(--border)] p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                <?php
                                    if ($level == 3) {
                                        // Company
                                        if (isset($logo) && $logo) {
                                            echo '<img src="../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                        } else {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                        }
                                    } else {
                                        // User
                                        if (isset($profile_picture) && $profile_picture) {
                                            echo '<img src="../src/profile_picture/' . htmlspecialchars($profile_picture) . '" alt="User avatar" class="h-full w-full object-cover" />';
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
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
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
                        <a href="../logout.php"
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
                                            if (isset($logo) && $logo) {
                                                echo '<img src="../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                            } else {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                            }
                                        } else {
                                            // User
                                            if (isset($profile_picture) && $profile_picture) {
                                                echo '<img src="../src/profile_picture/' . htmlspecialchars($profile_picture) . '" alt="User avatar" class="h-full w-full object-cover" />';
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
                                    <a href="../logout.php" class="block px-4 py-2 text-sm hover:bg-[var(--secondary)]">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <?php
                if ($level !== 3) {
                    echo '<div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">';
                } else {
                    echo '<div class="flex-1 p-4 overflow-auto">';
                }
            ?>
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                        <?php
                            if ($level == 2) {
                                echo '<p class="text-muted-foreground">Welcome back! Here\'s an overview of your internship journey.</p>';
                            }
                        ?>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <?php
                            if ($level == 1) {
                                include_once('../include/dbconn.php');
                                $today = date('Y-m-d');
                                $user_id = $_SESSION['user_id'];

                                $query = "SELECT * FROM user ORDER BY user_id DESC LIMIT 5";
                                $result = mysqli_query($dbconn, $query);
                        ?>

                                <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">
                                    <div class="flex flex-col space-y-1.5 p-6">
                                        <h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">
                                            <i class="fa-solid fa-clock-rotate-left h-5 w-5 text-[var(--primary)]"></i> &nbsp;Recent User Registrations
                                        </h3>
                                    </div>
                                    <div class="p-6 pt-0">
                                        <div class="space-y-4 max-h-72 overflow-y-auto" style="max-width: 400px;">
                                            <?php
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($rec_user = mysqli_fetch_assoc($result)) {
                                                        // Display each event with company name from join
                                                        ?>
                                                        <div class="flex items-start gap-3">
                                                            <div class="bg-[var(--primary-light)] text-[var(--primary)] rounded-md p-2 flex items-center justify-center">
                                                                <?php
                                                                    if ($rec_user['level_id'] == 1) {
                                                                        echo '<i class="fa-solid fa-user-tie"></i>';
                                                                    } else if ($rec_user['level_id'] == 2) {
                                                                        echo '<i class="fa-solid fa-user"></i>';
                                                                    } else if ($rec_user['level_id'] == 3) {
                                                                        echo '<i class="fa-solid fa-building"></i>';
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium"><?php echo $rec_user['email']; ?></p>
                                                                <div class="flex items-center gap-2 text-sm text-[var(--muted)]">
                                                                    <span><?php echo $rec_user['full_name'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<div class="text-sm text-[var(--muted)]">No user registered.</div>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        ?>

                        <?php
                            if ($level == 1) {
                                include_once('../include/dbconn.php');
                                $today = date('Y-m-d');
                                $user_id = $_SESSION['user_id'];

                                $query = "SELECT * FROM company ORDER BY company_id DESC LIMIT 5";
                                $result = mysqli_query($dbconn, $query);
                        ?>

                                <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">
                                    <div class="flex flex-col space-y-1.5 p-6">
                                        <h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">
                                            <i class="fa-solid fa-clock-rotate-left h-5 w-5 text-[var(--primary)]"></i> &nbsp;Recent Company Registrations
                                        </h3>
                                    </div>
                                    <div class="p-6 pt-0">
                                        <div class="space-y-4 max-h-72 overflow-y-auto" style="max-width: 400px;">
                                            <!-- Announcements populated from database -->
                                            <?php
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($rec_company = mysqli_fetch_assoc($result)) {
                                                        // Display each event with company name from join
                                                        ?>
                                                        <div class="flex items-start gap-3">
                                                            <div class="bg-[var(--primary-light)] text-[var(--primary)] rounded-md p-2 flex items-center justify-center">
                                                                <i class="fa-solid fa-building"></i>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium"><?php echo $rec_company['company_name']; ?></p>
                                                                <div class="flex items-center gap-2 text-sm text-[var(--muted)]">
                                                                    <span><?php echo $rec_company['email'] ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<div class="text-sm text-[var(--muted)]">No user registered.</div>';
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
                                include_once('../include/dbconn.php');
                                $today = date('Y-m-d');
                                $user_id = $_SESSION['user_id'];

                                $query = "SELECT a.*, c.company_name
                                        FROM announcement a 
                                        JOIN company c ON a.company_id = c.company_id";
                                $result = mysqli_query($dbconn, $query);
                        ?>

                                <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">
                                    <div class="flex flex-col space-y-1.5 p-6">
                                        <h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">
                                            <i class="fas fa-calendar h-5 w-5 text-[var(--primary)]"></i>
                                            Announcements
                                        </h3>
                                        <p class="text-sm text-[var(--muted)]">Your scheduled events and deadlines</p>
                                    </div>
                                    <div class="p-6 pt-0">
                                        <div class="space-y-4 max-h-72 overflow-y-auto" style="max-width: 400px;">
                                            <!-- Announcements populated from database -->
                                            <?php
                                                if (mysqli_num_rows($result) > 0) {
                                                    while ($announcement = mysqli_fetch_assoc($result)) {
                                                        // Display each event with company name from join
                                                        ?>
                                                        <div class="flex items-start gap-3">
                                                            <div class="bg-[var(--primary-light)] text-[var(--primary)] rounded-md p-2 flex items-center justify-center">
                                                                <i class="fas fa-calendar h-5 w-5 flex items-center justify-center"></i>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium"><?php echo $announcement['announcement_title']; ?></p>
                                                                <div class="flex items-center gap-2 text-sm text-[var(--muted)]">
                                                                    <span><?php echo date('F j, Y', strtotime($announcement['created_at'])); ?></span>
                                                                    <span>•</span>
                                                                    <span><?php echo $announcement['company_name']; ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<div class="text-sm text-[var(--muted)]">No announcements found.</div>';
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
                                include_once('../include/dbconn.php');
                                $user_id = $_SESSION['user_id'];
                                $query = "SELECT a.*, v.title AS position, c.company_name, s.status_name, a.applied_at FROM applicant a
                                    JOIN vacancy v ON a.vacancy_id = v.vacancy_id
                                    JOIN company c ON v.company_id = c.company_id
                                    JOIN application_status s ON a.application_status_id = s.application_status_id
                                    WHERE a.user_id = '$user_id'
                                    ORDER BY a.applied_at DESC
                                    LIMIT 3";
                                $result = mysqli_query($dbconn, $query);
                                echo '<div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">';
                                echo '<div class="flex flex-col space-y-1.5 p-6">';
                                echo '<h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">';
                                echo '<i class="fas fa-briefcase h-5 w-5 text-[var(--primary)]"></i>';
                                echo 'Recent Applications';
                                echo '</h3>';
                                echo '<p class="text-sm text-[var(--muted)]">Your recent internship applications</p>';
                                echo '</div>';
                                echo '<div class="p-6 pt-0">';
                                echo '<div class="space-y-4"';
                                echo '<!-- Applications populated in PHP-->';
                                if (mysqli_num_rows($result) > 0) {
                                    while ($app = mysqli_fetch_assoc($result)) {
                                        $status = $app['status_name'];
                                        $statusClasses = '';
                                        $progressValue = 0;
                                        if ($status == 'Applied') { $statusClasses = 'bg-blue-100 text-blue-800'; $progressValue = 33; }
                                        elseif ($status == 'Interview') { $statusClasses = 'bg-yellow-100 text-yellow-800'; $progressValue = 66; }
                                        elseif ($status == 'Rejected') { $statusClasses = 'bg-red-100 text-red-800'; $progressValue = 100; }
                                        elseif ($status == 'Offered') { $statusClasses = 'bg-green-100 text-green-800'; $progressValue = 100; }
                                        echo '<div class="space-y-1.5">';
                                        echo '<div class="flex items-center justify-between text-sm">';
                                        echo '<p class="font-medium">' . $app['position'] . '</p>';
                                        echo '<span class="px-2 py-0.5 rounded-full text-xs font-medium ' . $statusClasses . '">' . $status . '</span>';
                                        echo '</div>';
                                        echo '<div class="text-xs text-[var(--muted)]">' . $app['company_name'] . '</div>';
                                        echo '<div class="w-full bg-[var(--secondary)] rounded-full h-1">';
                                        echo '<div class="bg-[var(--primary)] h-1 rounded-full" style="width: ' . $progressValue . '%"></div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="text-sm text-[var(--muted)]">No recent applications found.</div>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        ?>

                        <?php
                            if ($level == 3) { // Company user
                                $company_id = $_SESSION['company_id'];

                                // Latest Applications for this company
                                $query = "SELECT a.*, u.full_name, u.email, v.title 
                                            FROM applicant a 
                                            JOIN vacancy v ON a.vacancy_id = v.vacancy_id 
                                            JOIN user u ON a.user_id = u.user_id 
                                            WHERE v.company_id = $company_id 
                                            ORDER BY a.applied_at DESC LIMIT 5";
                                $result = mysqli_query($dbconn, $query);
                                echo '<div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">';
                                echo '<div class="flex flex-col space-y-1.5 p-6">';
                                echo '<h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">';
                                echo '<i class="fa-solid fa-clock-rotate-left h-5 w-5 text-[var(--primary)]"></i> &nbsp;Latest Applications';
                                echo '</h3>';
                                echo '</div>';
                                echo '<div class="p-6 pt-0">';
                                echo '<div class="space-y-4 max-h-72 overflow-y-auto">';
                                if (mysqli_num_rows($result) > 0) {
                                    while ($rec_app = mysqli_fetch_assoc($result)) {
                                        echo '<div class="flex items-center gap-4 bg-[var(--background)] rounded-lg shadow p-4 border border-[var(--border)] hover:shadow-md transition-all w-full">';
                                        echo '  <div class="flex-shrink-0 h-12 w-12 rounded-full bg-[var(--primary-light)] flex items-center justify-center">';
                                        echo '    <i class="fa-solid fa-user text-2xl text-[var(--primary)]"></i>';
                                        echo '  </div>';
                                        echo '  <div class="flex-1">';
                                        echo '    <div class="flex items-center justify-between">';
                                        echo '      <p class="font-semibold text-lg">' . $rec_app['full_name'] . '</p>';
                                        echo '    </div>';
                                        echo '    <p class="text-sm text-gray-500">' . $rec_app['email'] . '</p>';
                                        echo '    <div class="mt-1 text-xs text-[var(--muted)]">applied for <strong>' . $rec_app['title'] . '</strong></div>';
                                        echo '  </div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="text-sm text-[var(--muted)]">No recent applications found.</div>';
                                }
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';

                                // Analytics for company
                                $applicantQuery = "SELECT v.vacancy_id, v.title, COUNT(a.applicant_id) as total_applicants 
                                    FROM vacancy v 
                                    LEFT JOIN applicant a ON v.vacancy_id = a.vacancy_id 
                                    WHERE v.company_id = $company_id 
                                    GROUP BY v.vacancy_id";
                                $vacancyQuery = "SELECT COUNT(*) as total_vacancies 
                                    FROM vacancy 
                                    WHERE company_id = $company_id";
                                $applicantResult = mysqli_query($dbconn, $applicantQuery);
                                $vacancyResult = mysqli_query($dbconn, $vacancyQuery);
                        ?>
                            <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">
                                <div class="flex flex-col space-y-1.5 p-6">
                                    <h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">
                                        <i class="fa-solid fa-chart-pie h-5 w-5 text-[var(--primary)]"></i> &nbsp;Analytics
                                    </h3>
                                    <p class="text-sm text-[var(--muted)]">Analytics between total vacancies and total applicants</p>
                                    <div id="chartdiv" style="width: 100%; height: 300px;"></div>
                                </div>
                            </div>
                            <script>
                                am5.ready(function() {
                                    // Create root element
                                    // https://www.amcharts.com/docs/v5/getting-started/#Root_element
                                    var root = am5.Root.new("chartdiv");

                                    // Set themes
                                    // https://www.amcharts.com/docs/v5/concepts/themes/
                                    root.setThemes([
                                        am5themes_Animated.new(root)
                                    ]);

                                    // Create chart
                                    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
                                    var chart = root.container.children.push(
                                        am5percent.PieChart.new(root, {
                                            endAngle: 270
                                        })
                                    );

                                    // Create series
                                    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
                                    var series = chart.series.push(
                                        am5percent.PieSeries.new(root, {
                                            valueField: "value",
                                            categoryField: "category",
                                            endAngle: 270
                                        })
                                    );

                                    series.states.create("hidden", {
                                        endAngle: -90
                                    });

                                    // Set data
                                    // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
                                    series.data.setAll([
                                        {
                                            category: "Total Vacancy",
                                            value: 
                                                <?php
                                                    $totalVacancies = mysqli_fetch_assoc($vacancyResult)['total_vacancies'];
                                                    echo $totalVacancies;
                                                ?>
                                        }, {
                                            category: "Total Applicants",
                                            value: 
                                                <?php
                                                    $totalApplicants = 0;
                                                    while ($row = mysqli_fetch_assoc($applicantResult)) {
                                                        $totalApplicants += $row['total_applicants'];
                                                    }
                                                    echo $totalApplicants;
                                                ?>
                                        }
                                    ]);

                                    series.appear(1000, 100);

                                }); // end am5.ready()
                            </script>
                        <?php
                            }
                        ?>

                    </div>

                    <?php
                        if ($level == 1) { // Assuming admin level is 0
                            include_once('../include/dbconn.php');
                            // Platform statistics
                            $userCount = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) as total FROM user"))['total'];
                            $companyCount = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) as total FROM company"))['total'];
                            $internshipCount = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) as total FROM vacancy"))['total'];
                            $applicationCount = mysqli_fetch_assoc(mysqli_query($dbconn, "SELECT COUNT(*) as total FROM applicant"))['total'];
                            echo '<div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md mb-6">';
                            echo '<div class="flex flex-col space-y-1.5 p-6">';
                            echo '<h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">';
                            echo '<i class="fa-solid fa-chart-simple h-5 w-5 text-[var(--primary)]"></i>';
                            echo 'Platform Statistics';
                            echo '</h3>';
                            echo '<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4">';
                            echo '<div class="text-center"><div class="text-3xl font-bold">' . $userCount . '</div><div class="text-sm text-[var(--muted)]">Users</div></div>';
                            echo '<div class="text-center"><div class="text-3xl font-bold">' . $companyCount . '</div><div class="text-sm text-[var(--muted)]">Companies</div></div>';
                            echo '<div class="text-center"><div class="text-3xl font-bold">' . $internshipCount . '</div><div class="text-sm text-[var(--muted)]">Internships</div></div>';
                            echo '<div class="text-center"><div class="text-3xl font-bold">' . $applicationCount . '</div><div class="text-sm text-[var(--muted)]">Applications</div></div>';
                            echo '</div>';
                            echo '</div>';
                            // Recent activity (latest 5 applications)
                            $recentApps = mysqli_query($dbconn, "SELECT a.*, u.full_name as user_name, v.title as internship_title FROM applicant a JOIN user u ON a.user_id = u.user_id JOIN vacancy v ON a.vacancy_id = v.vacancy_id ORDER BY a.applied_at DESC LIMIT 5");
                            echo '<div class="p-6 pt-0">';
                            echo '<h4 class="text-lg font-semibold mb-2">Recent Applications</h4>';
                            if (mysqli_num_rows($recentApps) > 0) {
                                echo '<ul class="divide-y">';
                                while ($app = mysqli_fetch_assoc($recentApps)) {
                                    echo '<li class="py-2 flex justify-between items-center">';
                                    echo '<span><strong>' . $app['user_name'] . '</strong> applied for <strong>' . $app['internship_title'] . '</strong></span>';
                                    echo '<span class="text-xs text-[var(--muted)]">' . date('M d, Y', strtotime($app['applied_at'])) . '</span>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<div class="text-sm text-[var(--muted)]">No recent applications found.</div>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }
                    ?>

                    <?php
                        if ($level == 2) {
                            include_once('../include/dbconn.php');
                            $query = "SELECT v.*, c.company_name FROM vacancy v JOIN company c ON v.company_id = c.company_id WHERE v.vacancy_status_id = 1 ORDER BY v.created_at DESC LIMIT 6";
                            $result = mysqli_query($dbconn, $query);
                            echo '<div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm transition-all hover:shadow-md">';
                            echo '<div class="flex flex-col space-y-1.5 p-6">';
                            echo '<h3 class="flex items-center gap-2 text-2xl font-semibold leading-none tracking-tight">';
                            echo '<i class="fas fa-building h-5 w-5 text-[var(--primary)]"></i>';
                            echo 'Recommended Internships';
                            echo '</h3>';
                            echo '<p class="text-sm text-[var(--muted)]">Based on your profile and preferences</p>';
                            echo '</div>';
                            echo '<div class="p-6 pt-0">';
                            echo '<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">';
                            if (mysqli_num_rows($result) > 0) {
                                while ($internship = mysqli_fetch_assoc($result)) {
                                    echo '<div class="rounded-lg border border-[var(--border)] p-3 transition-all hover:shadow">';
                                    echo '<div class="flex items-center justify-between">';
                                    echo '<div class="flex items-center gap-2">';
                                    echo '<div class="h-10 w-10 rounded bg-[var(--secondary)] flex items-center justify-center">';
                                    echo '<i class="fas fa-building h-5 w-5 text-gray-500 flex items-center justify-center"></i>';
                                    echo '</div>';
                                    echo '<div>';
                                    echo '<p class="font-medium">' . $internship['title'] . '</p>';
                                    echo '<p class="text-sm text-[var(--muted)]">' . $internship['company_name'] . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '<div class="mt-2 text-xs">';
                                    echo '<span class="inline-block bg-[var(--secondary)] rounded-full px-2 py-1 mr-1">Remote</span>';
                                    echo '<span class="inline-block bg-[var(--secondary)] rounded-full px-2 py-1 mr-1">3 months</span>';
                                    echo '<span class="inline-block bg-[var(--secondary)] rounded-full px-2 py-1">Paid</span>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="text-sm text-[var(--muted)]">No recommended internships found.</div>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/Dashboard.js"></script>
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