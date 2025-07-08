
<?php
session_start();
include('../../include/dbconn.php');

if (isset($_SESSION['company_id'])) {
    $company_id = $_SESSION['company_id'];
    $level = 3;
    $stmt = $dbconn->prepare("SELECT * FROM company WHERE company_id = ?");
    $stmt->bind_param("i", $company_id);
    $stmt->execute();
    $resultCompany = $stmt->get_result();
    if ($resultCompany && $resultCompany->num_rows > 0) {
        $company = $resultCompany->fetch_assoc();
        $company_name = $company['company_name'];
        $logo = $company['logo'];
        $full_name = $company_name;
    } else {
        header('Location: /dashboard/profile?error=no_company_profile');
        exit;
    }
} elseif (isset($_SESSION['user_id'])) {
    // Students/admins should not access this page
    header('Location: /dashboard');
    exit;
} else {
    header('Location: /login');
    exit;
}

// Get filter parameters
$vacancy_filter = isset($_GET['vacancy']) ? $_GET['vacancy'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$offer_filter = isset($_GET['offer']) ? $_GET['offer'] : '';

// Build the applications query with filters
$applications_query = "SELECT a.*, u.full_name as student_name, u.email as student_email, 
                        u.phone_number, u.campus, u.course, u.profile_picture as student_picture,
                        v.title as vacancy_title, v.role, v.elaun,
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
                        JOIN user u ON a.user_id = u.user_id
                        JOIN vacancy v ON a.vacancy_id = v.vacancy_id
                        JOIN company c ON v.company_id = c.company_id
                        WHERE c.company_id = ?";

$params = [$company_id];
$param_types = "i";

// Apply filters
if (!empty($vacancy_filter)) {
    $applications_query .= " AND v.vacancy_id = ?";
    $params[] = $vacancy_filter;
    $param_types .= "i";
}

if (!empty($status_filter)) {
    $applications_query .= " AND a.application_status_id = ?";
    $params[] = $status_filter;
    $param_types .= "i";
}

if (!empty($offer_filter)) {
    $applications_query .= " AND a.offer_status_id = ?";
    $params[] = $offer_filter;
    $param_types .= "i";
}

$applications_query .= " ORDER BY a.applied_at DESC";

$stmt = $dbconn->prepare($applications_query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$applications_result = $stmt->get_result();

// Get company vacancies for filter dropdown
$stmt = $dbconn->prepare("SELECT v.vacancy_id, v.title FROM vacancy v 
                    WHERE v.company_id = ? 
                    ORDER BY v.title");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$vacancies_result = $stmt->get_result();

// Handle success/error messages
$message = '';
$message_type = '';
if (isset($_GET['success'])) {
    $message_type = 'success';
    switch ($_GET['success']) {
        case 'status_updated':
            $message = 'Application status updated successfully!';
            break;
        case 'offer_updated':
            $message = 'Offer status updated successfully!';
            break;
    }
}
if (isset($_GET['error'])) {
    $message_type = 'error';
    switch ($_GET['error']) {
        case 'update_failed':
            $message = 'Failed to update status. Please try again.';
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                    if ($logo) {
                                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                    } else {
                                        echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
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
                                        if ($logo) {
                                            echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                        } else {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
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
            </header>            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Applications Management</h1>
                            <p class="text-[var(--muted)]">Manage applicants for your internship positions at <?php echo $company_name; ?>.</p>
                        </div>
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
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] p-4">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="vacancy" class="block text-sm font-medium text-[var(--foreground)] mb-2">Filter by Vacancy</label>
                                <select name="vacancy" id="vacancy" class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] bg-[var(--background)] text-[var(--foreground)]">
                                    <option value="">All Vacancies</option>
                                    <?php
                                    mysqli_data_seek($vacancies_result, 0);
                                    while ($vacancy = $vacancies_result->fetch_assoc()) {
                                        $selected = ($vacancy_filter == $vacancy['vacancy_id']) ? 'selected' : '';
                                        echo '<option value="' . $vacancy['vacancy_id'] . '" ' . $selected . '>' . $vacancy['title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-[var(--foreground)] mb-2">Application Status</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] bg-[var(--background)] text-[var(--foreground)]">
                                    <option value="">All Statuses</option>
                                    <option value="1" <?php echo ($status_filter == '1') ? 'selected' : ''; ?>>Applied</option>
                                    <option value="2" <?php echo ($status_filter == '2') ? 'selected' : ''; ?>>Interview</option>
                                    <option value="3" <?php echo ($status_filter == '3') ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="4" <?php echo ($status_filter == '4') ? 'selected' : ''; ?>>Offered</option>
                                </select>
                            </div>
                            <div>
                                <label for="offer" class="block text-sm font-medium text-[var(--foreground)] mb-2">Offer Status</label>
                                <select name="offer" id="offer" class="w-full px-3 py-2 border border-[var(--border)] rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary)] bg-[var(--background)] text-[var(--foreground)]">
                                    <option value="">All Offers</option>
                                    <option value="1" <?php echo ($offer_filter == '1') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="2" <?php echo ($offer_filter == '2') ? 'selected' : ''; ?>>Accepted</option>
                                    <option value="3" <?php echo ($offer_filter == '3') ? 'selected' : ''; ?>>Declined</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] transition">
                                    <i class="fas fa-filter mr-2"></i>Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Applications List -->
                    <div class="bg-[var(--background)] rounded-lg border border-[var(--border)] shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-semibold">Applications</h2>
                                <span class="text-sm text-[var(--muted)]">
                                    <?php echo $applications_result->num_rows; ?> application(s) found
                                </span>
                            </div>

                            <?php if ($applications_result->num_rows > 0): ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-[var(--border)]">
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Student</th>
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Position</th>
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Applied Date</th>
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Application Status</th>
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Offer Status</th>
                                                <th class="text-left py-3 px-4 font-medium text-[var(--foreground)]">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($app = $applications_result->fetch_assoc()): ?>
                                                <tr class="border-b border-[var(--border)] hover:bg-[var(--secondary)]">
                                                    <td class="py-4 px-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-10 w-10 rounded-full overflow-hidden">
                                                                <?php if ($app['student_picture']): ?>
                                                                    <img src="../../src/profile_picture/<?php echo $app['student_picture']; ?>" 
                                                                         alt="Student" class="h-full w-full object-cover">
                                                                <?php else: ?>
                                                                    <img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" 
                                                                         alt="Student" class="h-full w-full object-cover">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-[var(--foreground)]"><?php echo $app['student_name']; ?></p>
                                                                <p class="text-sm text-[var(--muted)]"><?php echo $app['student_email']; ?></p>
                                                                <p class="text-xs text-[var(--muted)]"><?php echo $app['campus']; ?> - <?php echo $app['course']; ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-4 px-4">
                                                        <p class="font-medium text-[var(--foreground)]"><?php echo $app['vacancy_title']; ?></p>
                                                        <p class="text-sm text-[var(--muted)]"><?php echo $app['role']; ?></p>
                                                        <?php if ($app['elaun']): ?>
                                                            <p class="text-xs text-[var(--muted)]">RM <?php echo $app['elaun']; ?>/month</p>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-4 px-4">
                                                        <p class="text-sm text-[var(--foreground)]"><?php echo date('M j, Y', strtotime($app['applied_at'])); ?></p>
                                                        <p class="text-xs text-[var(--muted)]"><?php echo date('g:i A', strtotime($app['applied_at'])); ?></p>
                                                    </td>
                                                    <td class="py-4 px-4">
                                                        <form method="POST" action="../../process.php" class="inline">
                                                            <input type="hidden" name="update_application_status" value="1">
                                                            <input type="hidden" name="applicant_id" value="<?php echo $app['user_id']; ?>">
                                                            <input type="hidden" name="vacancy_id" value="<?php echo $app['vacancy_id']; ?>">
                                                            <select name="application_status_id" 
                                                                    class="px-2 py-1 border border-[var(--border)] rounded text-xs bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-1 focus:ring-[var(--primary)]">
                                                                <option value="1" <?php echo ($app['application_status_id'] == 1) ? 'selected' : ''; ?>>Applied</option>
                                                                <option value="2" <?php echo ($app['application_status_id'] == 2) ? 'selected' : ''; ?>>Interview</option>
                                                                <option value="3" <?php echo ($app['application_status_id'] == 3) ? 'selected' : ''; ?>>Rejected</option>
                                                                <option value="4" <?php echo ($app['application_status_id'] == 4) ? 'selected' : ''; ?>>Offered</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td class="py-4 px-4">                                                        <?php if ($app['offer_status_id'] && $app['offer_status_id'] > 0): // Show offer status if it exists ?>
                                                            <form method="POST" action="../../process.php" class="inline">
                                                                <input type="hidden" name="update_offer_status" value="1">
                                                                <input type="hidden" name="applicant_id" value="<?php echo $app['user_id']; ?>">
                                                                <input type="hidden" name="vacancy_id" value="<?php echo $app['vacancy_id']; ?>">                                                                <select name="offer_status_id" 
                                                                        class="px-2 py-1 border border-[var(--border)] rounded text-xs bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-1 focus:ring-[var(--primary)]">
                                                                    <option value="1" <?php echo ($app['offer_status_id'] == 1) ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="2" <?php echo ($app['offer_status_id'] == 2) ? 'selected' : ''; ?>>Accepted</option>
                                                                    <option value="3" <?php echo ($app['offer_status_id'] == 3) ? 'selected' : ''; ?>>Declined</option>
                                                                </select>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-xs text-[var(--muted)]">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-4 px-4">
                                                        <div class="flex gap-2">
                                                            <?php if ($app['resume_url']): ?>
                                                                <a href="../../src/resume/<?php echo $app['resume_url']; ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded hover:bg-blue-200 transition">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                    Resume
                                                                </a>
                                                            <?php endif; ?>
                                                            <button onclick="openStudentModal('<?php echo $app['user_id']; ?>', '<?php echo $app['student_name']; ?>', '<?php echo $app['student_email']; ?>', '<?php echo $app['phone_number']; ?>', '<?php echo $app['campus']; ?>', '<?php echo $app['course']; ?>')"
                                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded hover:bg-green-200 transition">
                                                                <i class="fas fa-eye"></i>
                                                                View
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12">
                                    <i class="fas fa-inbox text-4xl text-[var(--muted)] mb-4"></i>
                                    <h3 class="text-lg font-medium text-[var(--foreground)] mb-2">No Applications Found</h3>
                                    <p class="text-[var(--muted)]">
                                        <?php if (!empty($vacancy_filter) || !empty($status_filter) || !empty($offer_filter)): ?>
                                            No applications match your current filters.
                                        <?php else: ?>
                                            You haven't received any applications for your internships yet.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>        </div>
    </div>

    <!-- Student Details Modal -->
    <div id="studentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-[var(--background)] rounded-lg shadow-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-[var(--foreground)]">Student Details</h3>
                        <button onclick="closeStudentModal()" class="text-[var(--muted)] hover:text-[var(--foreground)]">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div id="studentDetails" class="space-y-3">
                        <!-- Student details will be populated here -->
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button onclick="closeStudentModal()" class="px-4 py-2 bg-[var(--secondary)] text-[var(--foreground)] rounded-md hover:bg-[var(--muted)] transition">
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

        // Student modal functions
        function openStudentModal(userId, name, email, phone, campus, course) {
            const modal = document.getElementById('studentModal');
            const detailsContainer = document.getElementById('studentDetails');
            
            detailsContainer.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas fa-user text-[var(--muted)]"></i>
                    <span class="font-medium">Name:</span>
                    <span>${name}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-envelope text-[var(--muted)]"></i>
                    <span class="font-medium">Email:</span>
                    <span>${email}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-phone text-[var(--muted)]"></i>
                    <span class="font-medium">Phone:</span>
                    <span>${phone || 'Not provided'}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-university text-[var(--muted)]"></i>
                    <span class="font-medium">Campus:</span>
                    <span>${campus || 'Not provided'}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-[var(--muted)]"></i>
                    <span class="font-medium">Course:</span>
                    <span>${course || 'Not provided'}</span>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeStudentModal() {
            const modal = document.getElementById('studentModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('studentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStudentModal();
            }
        });

        // Store original values for reset on cancel
        const originalValues = new Map();
        
        // Store original values when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[name="application_status_id"], select[name="offer_status_id"]').forEach(function(select) {
                originalValues.set(select, select.value);
            });
        });

        // Confirmation for status changes
        document.addEventListener('change', function(e) {
            if (e.target.tagName === 'SELECT' && (e.target.name === 'application_status_id' || e.target.name === 'offer_status_id')) {
                const statusText = e.target.options[e.target.selectedIndex].text;
                const originalValue = originalValues.get(e.target);
                
                if (!confirm(`Are you sure you want to change this to "${statusText}"?`)) {
                    // Reset to original value and prevent submission
                    e.target.value = originalValue;
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                } else {
                    // User confirmed, update the stored value and submit
                    originalValues.set(e.target, e.target.value);
                    e.target.form.submit();
                }
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