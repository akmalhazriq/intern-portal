
<?php
    session_start();
    // Include database connection settings
    include('../../include/dbconn.php');

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
        // Company should not access admin reports, redirect
        header('Location: /dashboard');
        exit;
    } else {
        header('Location: /login');
        exit;
    }

    // Get current date for report
    $report_date = date('F j, Y');
    $report_time = date('g:i A');

    // Get selected semester from GET parameter, default to all semesters
    $selected_semester_id = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
    
    // Fetch all semesters for the dropdown
    $semesters_query = "SELECT * FROM semester ORDER BY start_date DESC";
    $semesters_result = mysqli_query($dbconn, $semesters_query);

    // Build semester filter condition
    $semester_filter = "";
    $semester_join = "";
    if ($selected_semester_id > 0) {
        $semester_filter = " AND v.semester_id = $selected_semester_id";
        $semester_join = " LEFT JOIN vacancy v ON a.vacancy_id = v.vacancy_id";
    }

    // Get selected semester name for report header
    $selected_semester_name = "All Semesters";
    if ($selected_semester_id > 0) {
        $semester_name_query = "SELECT semester_name FROM semester WHERE semester_id = $selected_semester_id";
        $semester_name_result = mysqli_query($dbconn, $semester_name_query);
        if ($semester_name_result && mysqli_num_rows($semester_name_result) > 0) {
            $semester_row = mysqli_fetch_assoc($semester_name_result);
            $selected_semester_name = $semester_row['semester_name'];
        }
    }

    // Fetch statistics
    // Total counts
    $total_users_query = "SELECT COUNT(*) as total FROM user";
    $total_users_result = mysqli_query($dbconn, $total_users_query);
    $total_users = mysqli_fetch_assoc($total_users_result)['total'];

    $total_students_query = "SELECT COUNT(*) as total FROM user WHERE level_id = 2";
    $total_students_result = mysqli_query($dbconn, $total_students_query);
    $total_students = mysqli_fetch_assoc($total_students_result)['total'];

    $total_companies_query = "SELECT COUNT(*) as total FROM company";
    $total_companies_result = mysqli_query($dbconn, $total_companies_query);
    $total_companies = mysqli_fetch_assoc($total_companies_result)['total'];

    $verified_companies_query = "SELECT COUNT(*) as total FROM company WHERE verified = 1";
    $verified_companies_result = mysqli_query($dbconn, $verified_companies_query);
    $verified_companies = mysqli_fetch_assoc($verified_companies_result)['total'];

    // Semester-filtered vacancy counts
    if ($selected_semester_id > 0) {
        $total_vacancies_query = "SELECT COUNT(*) as total FROM vacancy WHERE semester_id = $selected_semester_id";
        $active_vacancies_query = "SELECT COUNT(*) as total FROM vacancy WHERE vacancy_status_id = 1 AND semester_id = $selected_semester_id";
    } else {
        $total_vacancies_query = "SELECT COUNT(*) as total FROM vacancy";
        $active_vacancies_query = "SELECT COUNT(*) as total FROM vacancy WHERE vacancy_status_id = 1";
    }
    $total_vacancies_result = mysqli_query($dbconn, $total_vacancies_query);
    $total_vacancies = mysqli_fetch_assoc($total_vacancies_result)['total'];
    $active_vacancies_result = mysqli_query($dbconn, $active_vacancies_query);
    $active_vacancies = mysqli_fetch_assoc($active_vacancies_result)['total'];

    // Semester-filtered application counts
    if ($selected_semester_id > 0) {
        $total_applications_query = "SELECT COUNT(*) as total FROM applicant a 
                                    LEFT JOIN vacancy v ON a.vacancy_id = v.vacancy_id 
                                    WHERE v.semester_id = $selected_semester_id";
    } else {
        $total_applications_query = "SELECT COUNT(*) as total FROM applicant";
    }
    $total_applications_result = mysqli_query($dbconn, $total_applications_query);
    $total_applications = mysqli_fetch_assoc($total_applications_result)['total'];

    $total_announcements_query = "SELECT COUNT(*) as total FROM announcement";
    $total_announcements_result = mysqli_query($dbconn, $total_announcements_query);
    $total_announcements = mysqli_fetch_assoc($total_announcements_result)['total'];

    // Application status breakdown (semester-filtered)
    if ($selected_semester_id > 0) {
        $application_status_query = "SELECT ast.status_name, COUNT(a.user_id) as count 
                                    FROM application_status ast 
                                    LEFT JOIN applicant a ON ast.application_status_id = a.application_status_id 
                                    LEFT JOIN vacancy v ON a.vacancy_id = v.vacancy_id
                                    WHERE v.semester_id = $selected_semester_id OR a.user_id IS NULL
                                    GROUP BY ast.application_status_id, ast.status_name";
    } else {
        $application_status_query = "SELECT ast.status_name, COUNT(a.user_id) as count 
                                    FROM application_status ast 
                                    LEFT JOIN applicant a ON ast.application_status_id = a.application_status_id 
                                    GROUP BY ast.application_status_id, ast.status_name";
    }
    $application_status_result = mysqli_query($dbconn, $application_status_query);

    // Recent activities (semester-filtered)
    if ($selected_semester_id > 0) {
        $recent_applications_query = "SELECT u.full_name, v.title, c.company_name, a.applied_at 
                                    FROM applicant a 
                                    JOIN user u ON a.user_id = u.user_id 
                                    JOIN vacancy v ON a.vacancy_id = v.vacancy_id 
                                    JOIN company c ON v.company_id = c.company_id 
                                    WHERE v.semester_id = $selected_semester_id
                                    ORDER BY a.applied_at DESC LIMIT 10";
    } else {
        $recent_applications_query = "SELECT u.full_name, v.title, c.company_name, a.applied_at 
                                    FROM applicant a 
                                    JOIN user u ON a.user_id = u.user_id 
                                    JOIN vacancy v ON a.vacancy_id = v.vacancy_id 
                                    JOIN company c ON v.company_id = c.company_id 
                                    ORDER BY a.applied_at DESC LIMIT 10";
    }
    $recent_applications_result = mysqli_query($dbconn, $recent_applications_query);

    // Company statistics (semester-filtered)
    if ($selected_semester_id > 0) {
        $companies_with_vacancies_query = "SELECT c.company_name, COUNT(v.vacancy_id) as vacancy_count, 
                                            COUNT(a.user_id) as application_count 
                                            FROM company c 
                                            LEFT JOIN vacancy v ON c.company_id = v.company_id AND v.semester_id = $selected_semester_id
                                            LEFT JOIN applicant a ON v.vacancy_id = a.vacancy_id 
                                            WHERE c.verified = 1
                                            GROUP BY c.company_id, c.company_name 
                                            HAVING vacancy_count > 0
                                            ORDER BY vacancy_count DESC LIMIT 10";
    } else {
        $companies_with_vacancies_query = "SELECT c.company_name, COUNT(v.vacancy_id) as vacancy_count, 
                                            COUNT(a.user_id) as application_count 
                                            FROM company c 
                                            LEFT JOIN vacancy v ON c.company_id = v.company_id 
                                            LEFT JOIN applicant a ON v.vacancy_id = a.vacancy_id 
                                            WHERE c.verified = 1
                                            GROUP BY c.company_id, c.company_name 
                                            ORDER BY vacancy_count DESC LIMIT 10";
    }
    $companies_with_vacancies_result = mysqli_query($dbconn, $companies_with_vacancies_query);

    // Monthly application trends (last 12 months, semester-filtered)
    if ($selected_semester_id > 0) {
        $monthly_applications_query = "SELECT 
                                        DATE_FORMAT(a.applied_at, '%Y-%m') as month,
                                        COUNT(*) as applications
                                        FROM applicant a
                                        JOIN vacancy v ON a.vacancy_id = v.vacancy_id
                                        WHERE a.applied_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                                        AND v.semester_id = $selected_semester_id
                                        GROUP BY DATE_FORMAT(a.applied_at, '%Y-%m')
                                        ORDER BY month DESC";
    } else {
        $monthly_applications_query = "SELECT 
                                        DATE_FORMAT(applied_at, '%Y-%m') as month,
                                        COUNT(*) as applications
                                        FROM applicant 
                                        WHERE applied_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                                        GROUP BY DATE_FORMAT(applied_at, '%Y-%m')
                                        ORDER BY month DESC";
    }
    $monthly_applications_result = mysqli_query($dbconn, $monthly_applications_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Reports - Dashboard</title>
    <link rel="stylesheet" href="../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-break {
                page-break-after: always;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 20px !important;
            }
            #main-content {
                margin-left: 0 !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>

<body>
    <div class="min-h-[100dvh] flex w-full bg-[var(--secondary)] text-[var(--foreground)]">
        <!-- Sidebar -->
        <div class="fixed flex h-screen w-0 flex-col bg-[var(--background)] text-[var(--foreground)] border-r border-[var(--border)] transform transition-all duration-300 ease-in-out overflow-hidden z-20 no-print" id="sidebar">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
                                    <?php echo htmlspecialchars($full_name); ?>
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

        <!-- Header -->
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

            <!-- Report Content -->
            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto print-container">
                <div class="mx-auto space-y-6">
                    <!-- Header Section -->
                    <div class="flex flex-row items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">System Reports & Analytics</h1>
                            <p class="text-[var(--muted)]">Comprehensive overview of the internship portal<?php echo $selected_semester_id > 0 ? " for " . $selected_semester_name : ""; ?>.</p>
                        </div>
                        <div class="no-print">
                            <button onclick="window.print()" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                        </div>
                    </div>

                    <!-- Semester Filter -->
                    <div class="bg-[var(--background)] border border-[var(--border)] rounded-lg p-4 no-print">
                        <form method="GET" action="" class="flex items-center gap-4 flex-wrap">
                            <label for="semester" class="text-sm font-medium text-[var(--foreground)]">
                                <i class="fas fa-filter mr-2"></i>Filter by Semester:
                            </label>
                            <select name="semester" id="semester" 
                                    class="px-3 py-2 border border-[var(--border)] rounded-md bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                                    onchange="this.form.submit()">
                                <option value="0" <?php echo $selected_semester_id == 0 ? 'selected' : ''; ?>>All Semesters</option>
                                <?php 
                                    mysqli_data_seek($semesters_result, 0); // Reset pointer
                                    while ($semester = mysqli_fetch_assoc($semesters_result)): 
                                ?>
                                    <option value="<?php echo $semester['semester_id']; ?>" 
                                            <?php echo $selected_semester_id == $semester['semester_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($semester['semester_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <?php if ($selected_semester_id > 0): ?>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-filter mr-1"></i>
                                        Filtered: <?php echo htmlspecialchars($selected_semester_name); ?>
                                    </span>
                                    <a href="?" class="text-sm text-[var(--muted)] hover:text-[var(--primary)] underline">
                                        Clear Filter
                                    </a>
                                </div>
                            <?php endif; ?>
                            <noscript>
                                <button type="submit" class="px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                    Apply Filter
                                </button>
                            </noscript>
                        </form>
                    </div>

                    <!-- Print Header (only visible when printing) -->
                    <div class="print-only border-b-2 border-gray-300 pb-4 mb-6">
                        <div class="text-center">
                            <h1 class="text-2xl font-bold">FSKM Internship Portal</h1>
                            <h2 class="text-xl font-semibold mt-2">System Report<?php echo $selected_semester_id > 0 ? " - " . $selected_semester_name : ""; ?></h2>
                            <p class="text-gray-600 mt-2">Generated on <?php echo $report_date; ?> at <?php echo $report_time; ?></p>
                            <p class="text-gray-600">Generated by: <?php echo $full_name; ?> (Admin)</p>
                            <?php if ($selected_semester_id > 0): ?>
                                <p class="text-gray-600">Semester: <?php echo $selected_semester_name; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500 text-white">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">Total Users</p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $total_users; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-green-500 text-white">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">Students</p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $total_students; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-purple-500 text-white">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">Companies</p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $verified_companies; ?>/<?php echo $total_companies; ?></p>
                                    <p class="text-xs text-[var(--muted)]">Verified/Total</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-orange-500 text-white">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">
                                        Vacancies<?php echo $selected_semester_id > 0 ? " ($selected_semester_name)" : ""; ?>
                                    </p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $active_vacancies; ?>/<?php echo $total_vacancies; ?></p>
                                    <p class="text-xs text-[var(--muted)]">Active/Total</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">
                                        Applications<?php echo $selected_semester_id > 0 ? " ($selected_semester_name)" : ""; ?>
                                    </p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $total_applications; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-teal-500 text-white">
                                        <i class="fas fa-bullhorn"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-[var(--muted)]">Announcements</p>
                                    <p class="text-2xl font-semibold text-[var(--foreground)]"><?php echo $total_announcements; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status Breakdown -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-[var(--foreground)] mb-4">Application Status Distribution</h3>
                                <div class="space-y-3">
                                    <?php while ($status = mysqli_fetch_assoc($application_status_result)): ?>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-[var(--foreground)]"><?php echo $status['status_name']; ?></span>
                                            <span class="text-sm text-[var(--muted)]"><?php echo $status['count']; ?> applications</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" 
                                                 style="width: <?php echo $total_applications > 0 ? ($status['count'] / $total_applications) * 100 : 0; ?>%"></div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Application Trends -->
                        <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-[var(--foreground)] mb-4">Monthly Applications (Last 12 Months)</h3>
                                <div class="space-y-3">
                                    <?php while ($month_data = mysqli_fetch_assoc($monthly_applications_result)): ?>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-[var(--foreground)]"><?php echo date('M Y', strtotime($month_data['month'] . '-01')); ?></span>
                                            <span class="text-sm text-[var(--muted)]"><?php echo $month_data['applications']; ?> applications</span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Performance -->
                    <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-[var(--foreground)] mb-4">Top Companies by Activity</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--border)]">
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Company Name</th>
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Vacancies Posted</th>
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Applications Received</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($company = mysqli_fetch_assoc($companies_with_vacancies_result)): ?>
                                            <tr class="border-b border-[var(--border)]">
                                                <td class="py-3 px-4 text-[var(--foreground)]"><?php echo $company['company_name']; ?></td>
                                                <td class="py-3 px-4 text-[var(--muted)]"><?php echo $company['vacancy_count']; ?></td>
                                                <td class="py-3 px-4 text-[var(--muted)]"><?php echo $company['application_count']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Applications -->
                    <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-[var(--foreground)] mb-4">Recent Applications</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-[var(--border)]">
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Student Name</th>
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Position</th>
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Company</th>
                                            <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Applied Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($application = mysqli_fetch_assoc($recent_applications_result)): ?>
                                            <tr class="border-b border-[var(--border)]">
                                                <td class="py-3 px-4 text-[var(--foreground)]"><?php echo $application['full_name']; ?></td>
                                                <td class="py-3 px-4 text-[var(--muted)]"><?php echo $application['title']; ?></td>
                                                <td class="py-3 px-4 text-[var(--muted)]"><?php echo $application['company_name']; ?></td>
                                                <td class="py-3 px-4 text-[var(--muted)]"><?php echo date('M j, Y', strtotime($application['applied_at'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div class="print-only mt-8 pt-4 border-t-2 border-gray-300 text-center text-sm text-gray-600">
                        <p>This report was generated automatically by the FSKM Internship Portal System</p>
                        <p>For any questions or concerns, please contact the system administrator.</p>
                        <p class="mt-2">© <?php echo date('Y'); ?> FSKM Internship Portal. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/Dashboard.js"></script>
    <script>
        // Theme management
        (function() {
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', 'light');
            }
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
