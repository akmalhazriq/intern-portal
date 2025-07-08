<?php
    session_start();

    // Include database connection settings
    include('../../include/dbconn.php');

    if (isset($_SESSION['user_id'])) {
        // Student/Admin
        $user_id = $_SESSION['user_id'];
        $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
        $stmt = $dbconn->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $user = $result->fetch_assoc();
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
        // Company
        $company_id = $_SESSION['company_id'];
        $level = 3;
        $stmt = $dbconn->prepare("SELECT * FROM company WHERE company_id = ?");
        $stmt->bind_param("i", $company_id);
        $stmt->execute();
        $resultCompany = $stmt->get_result();
        if ($resultCompany && $resultCompany->num_rows > 0) {
            $company = $resultCompany->fetch_assoc();
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
        // Not logged in
        header('Location: /login');
        exit();
    }

    // For students, fetch their recent applications
    if ($level == 2) {
        $stmt = $dbconn->prepare("SELECT a.*, v.title as vacancy_title, v.role, v.elaun, v.description,
                                c.company_name, c.logo, c.banner, c.area as company_area, c.state as company_state,
                                CASE a.application_status_id 
                                    WHEN 1 THEN 'Applied'
                                    WHEN 2 THEN 'Interview'
                                    WHEN 3 THEN 'Rejected'
                                    WHEN 4 THEN 'Offered'
                                END as status_name,
                                CASE a.offer_status_id 
                                    WHEN 1 THEN 'Pending'
                                    WHEN 2 THEN 'Accepted'
                                    WHEN 3 THEN 'Declined'
                                END as offer_name
                                FROM applicant a
                                JOIN vacancy v ON a.vacancy_id = v.vacancy_id
                                JOIN company c ON v.company_id = c.company_id
                                WHERE a.user_id = ?
                                ORDER BY a.applied_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $applications_result = $stmt->get_result();
    }

    // Handle success/error messages
    $message = '';
    $message_type = '';
    if (isset($_GET['success'])) {
        $message_type = 'success';
        switch ($_GET['success']) {
            case 'offer_accepted':
                $message = 'Congratulations! You have successfully accepted the internship offer.';
                break;
            case 'offer_declined':
                $message = 'You have declined the internship offer.';
                break;
        }
    }
    if (isset($_GET['error'])) {
        $message_type = 'error';
        switch ($_GET['error']) {
            case 'update_failed':
                $message = 'Failed to update your response. Please try again.';
                break;
            case 'unauthorized':
                $message = 'You are not authorized to perform this action.';
                break;
        }
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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

                <div class="border-t border-[var(--border)] p-4 mt-auto">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="h-8 w-8 rounded-full overflow-hidden">
                                <?php
                                    if ($level == 3) {
                                        // Company - use company data already fetched
                                        if ($logo) {
                                            echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" />';
                                        } else {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                        }
                                    } else {
                                        // User - use already fetched profile_picture
                                        if ($profile_picture) {
                                            echo '<img src="../../src/profile_picture/' . htmlspecialchars($profile_picture ?? '') . '" alt="User avatar" class="h-full w-full object-cover" />';
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
                                    <?php echo htmlspecialchars($full_name ?? ''); ?>
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
                                                echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" />';
                                            } else {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                            }
                                        } else {
                                            // User
                                            if ($profile_picture) {
                                                echo '<img src="../../src/profile_picture/' . htmlspecialchars($profile_picture ?? '') . '" alt="User avatar" class="h-full w-full object-cover" />';
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
                if ($level == 2) {
            ?>
                    <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight">My Application Status</h1>
                                <p class="text-[var(--muted)]">Track your internship applications and respond to offers.</p>
                            </div>

                            <!-- Alert Messages -->
                            <?php if ($message): ?>
                                <div class="<?php echo $message_type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'; ?> border rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400'; ?> h-5 w-5"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium <?php echo $message_type === 'success' ? 'text-green-800' : 'text-red-800'; ?>"><?php echo $message; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>                            <!-- Applications List -->
                            <?php if ($applications_result && $applications_result->num_rows > 0): ?>
                                <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] shadow-sm">
                                    <div class="p-6">
                                        <h2 class="text-xl font-semibold mb-4">Your Applications</h2>
                                        <div class="space-y-4">
                                            <?php while ($app = $applications_result->fetch_assoc()): ?>
                                                <div class="border border-[var(--border)] rounded-lg p-4 hover:bg-[var(--secondary)] transition">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex-1">
                                                            <h3 class="text-lg font-medium text-[var(--foreground)]"><?php echo $app['vacancy_title']; ?></h3>
                                                            <p class="text-[var(--muted)]"><?php echo $app['company_name']; ?></p>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <!-- Application Status Badge -->
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                                <?php 
                                                                    switch($app['application_status_id']) {
                                                                        case 1: echo 'bg-blue-100 text-blue-800'; break;
                                                                        case 2: echo 'bg-yellow-100 text-yellow-800'; break;
                                                                        case 3: echo 'bg-red-100 text-red-800'; break;
                                                                        case 4: echo 'bg-green-100 text-green-800'; break;
                                                                        default: echo 'bg-gray-100 text-gray-800';
                                                                    }
                                                                ?>">
                                                                <i class="fas 
                                                                    <?php 
                                                                        switch($app['application_status_id']) {
                                                                            case 1: echo 'fa-clock'; break;
                                                                            case 2: echo 'fa-calendar-check'; break;
                                                                            case 3: echo 'fa-times-circle'; break;
                                                                            case 4: echo 'fa-check-circle'; break;
                                                                            default: echo 'fa-question-circle';
                                                                        }
                                                                    ?> mr-1"></i>
                                                                <?php echo $app['status_name']; ?>
                                                            </span>
                                                            
                                                            <!-- Applied Date -->
                                                            <span class="text-xs text-[var(--muted)]">
                                                                <?php echo date('M j, Y', strtotime($app['applied_at'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-sm">
                                                        <div>
                                                            <span class="text-[var(--muted)]">Department:</span>
                                                            <span class="font-medium ml-1"><?php echo $app['role']; ?></span>
                                                        </div>
                                                        <?php if ($app['elaun']): ?>
                                                            <div>
                                                                <span class="text-[var(--muted)]">Allowance:</span>
                                                                <span class="font-medium ml-1">RM <?php echo $app['elaun']; ?>/month</span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="text-[var(--muted)]">Location:</span>
                                                            <span class="font-medium ml-1"><?php echo $app['company_area']; ?>, <?php echo $app['company_state']; ?></span>
                                                        </div>
                                                    </div>

                                                    <!-- Offer Actions (Only show if status is "Offered" and offer is "Pending") -->
                                                    <?php if ($app['application_status_id'] == 4 && $app['offer_status_id'] == 1): ?>
                                                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                                            <h4 class="font-semibold text-green-800 flex items-center gap-2 mb-2">
                                                                <i class="fas fa-gift"></i>
                                                                Internship Offer Received!
                                                            </h4>
                                                            <p class="text-sm text-green-700 mb-3">
                                                                Congratulations! You have received an internship offer. Please respond below.
                                                            </p>
                                                            <div class="flex gap-3">
                                                                <form method="POST" action="../../process.php" class="inline">
                                                                    <input type="hidden" name="respond_to_offer" value="1">
                                                                    <input type="hidden" name="vacancy_id" value="<?php echo $app['vacancy_id']; ?>">
                                                                    <input type="hidden" name="offer_response" value="2">
                                                                    <button type="submit" onclick="return confirm('Are you sure you want to accept this offer?')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                                                        <i class="fas fa-check"></i>
                                                                        Accept Offer
                                                                    </button>
                                                                </form>
                                                                <form method="POST" action="../../process.php" class="inline">
                                                                    <input type="hidden" name="respond_to_offer" value="1">
                                                                    <input type="hidden" name="vacancy_id" value="<?php echo $app['vacancy_id']; ?>">
                                                                    <input type="hidden" name="offer_response" value="3">
                                                                    <button type="submit" onclick="return confirm('Are you sure you want to decline this offer?')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                                                        <i class="fas fa-times"></i>
                                                                        Decline Offer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($app['application_status_id'] == 4): ?>
                                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                                            <h4 class="font-semibold text-blue-800 flex items-center gap-2">
                                                                <i class="fas fa-info-circle"></i>
                                                                Offer Status: <?php echo $app['offer_name']; ?>
                                                            </h4>
                                                            <?php if ($app['offer_status_id'] == 2): ?>
                                                                <p class="text-sm text-blue-700 mt-1">
                                                                    You have accepted this internship offer. The company will contact you with further details.
                                                                </p>
                                                            <?php elseif ($app['offer_status_id'] == 3): ?>
                                                                <p class="text-sm text-blue-700 mt-1">
                                                                    You have declined this internship offer.
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- View Details Button -->
                                                    <div class="flex justify-end">
                                                        <button onclick="openApplicationModal('<?php echo $app['vacancy_id']; ?>', '<?php echo addslashes($app['vacancy_title']); ?>', '<?php echo addslashes($app['company_name']); ?>', '<?php echo addslashes($app['description']); ?>')"
                                                                class="inline-flex items-center gap-2 px-3 py-1 text-sm text-[var(--primary)] border border-[var(--primary)] rounded-md hover:bg-[var(--primary)] hover:text-white transition">
                                                            <i class="fas fa-eye"></i>
                                                            View Details
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] p-12 text-center">
                                    <i class="fas fa-clipboard-list text-4xl text-[var(--muted)] mb-4"></i>
                                    <h3 class="text-lg font-medium text-[var(--foreground)] mb-2">No Applications Yet</h3>
                                    <p class="text-[var(--muted)] mb-6">You haven't applied for any internships yet. Start exploring opportunities!</p>
                                    <a href="/dashboard/internships" class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                        <i class="fas fa-search"></i>
                                        Browse Internships
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php
                }
            ?>        </div>
    </div>

    <!-- Application Details Modal -->
    <div id="applicationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-[var(--background)] rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-[var(--foreground)]">Application Details</h3>
                        <button onclick="closeApplicationModal()" class="text-[var(--muted)] hover:text-[var(--foreground)]">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div id="applicationDetails" class="space-y-4">
                        <!-- Application details will be populated here -->
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button onclick="closeApplicationModal()" class="px-4 py-2 bg-[var(--secondary)] text-[var(--foreground)] rounded-md hover:bg-[var(--muted)] transition">
                            Close
                        </button>
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

        // Application modal functions
        function openApplicationModal(vacancyId, title, company, description) {
            const modal = document.getElementById('applicationModal');
            const detailsContainer = document.getElementById('applicationDetails');
            
            detailsContainer.innerHTML = `
                <div class="space-y-3">
                    <div>
                        <h4 class="font-semibold text-[var(--foreground)] mb-2">Position</h4>
                        <p class="text-[var(--foreground)]">${title}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-[var(--foreground)] mb-2">Company</h4>
                        <p class="text-[var(--foreground)]">${company}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-[var(--foreground)] mb-2">Job Description</h4>
                        <div class="text-[var(--foreground)] whitespace-pre-wrap bg-[var(--secondary)] p-3 rounded-md">${description}</div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeApplicationModal() {
            const modal = document.getElementById('applicationModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('applicationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });

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