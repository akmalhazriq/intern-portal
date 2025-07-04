
<?php
    session_start();
    // Include database connection settings
    include('../../include/dbconn.php');

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
            // For display in UI
            $full_name = $company_name;
        }
        $level = 3;
    } else {
        // Not logged in
        header('Location: /login');
        exit();
    }

    // Handle success/error messages from URL parameters
    $success_message = "";
    $error_message = "";
    
    if (isset($_GET['success'])) {
        switch ($_GET['success']) {
            case 'created':
                $success_message = "Announcement created successfully and sent to all students!";
                break;
        }
    }
    
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'create_failed':
                $error_message = "Error creating announcement. Please try again.";
                break;
            case 'missing_fields':
                $error_message = "Please fill in all required fields.";
                break;
            default:
                $error_message = "An unexpected error occurred.";
                break;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement - Dashboard</title>
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

        <div class="flex-1 ml-0 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
            <header class="bg-[var(--background)] border-b border-[var(--border)] sticky top-0 z-10">
                <div class="flex items-center justify-between px-5 py-2">
                    <div class="flex items-center">
                    </div>

                    <div class="flex items-center gap-3">
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

            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                <div class="max-w-4xl mx-auto space-y-6">
                    <div class="flex items-center gap-4">
                        <a href="/dashboard/announcement" class="text-[var(--muted)] hover:text-[var(--foreground)]">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Create New Announcement</h1>
                            <p class="text-[var(--muted)]">Send announcements to all students in the internship system.</p>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo $success_message; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <span><?php echo $error_message; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Create Announcement Form -->
                    <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                        <div class="p-6">
                            <form method="POST" action="../../process.php" class="space-y-6">
                                <!-- Announcement Title -->
                                <div class="space-y-2">
                                    <label for="announcement_title" class="text-sm font-medium">
                                        Announcement Title <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="announcement_title" 
                                        name="announcement_title" 
                                        required
                                        class="w-full px-3 py-2 border border-[var(--border)] rounded-md bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent"
                                        placeholder="Enter announcement title..."
                                        value=""
                                    >
                                </div>

                                <!-- Announcement Content -->
                                <div class="space-y-2">
                                    <label for="announcement_content" class="text-sm font-medium">
                                        Announcement Content
                                    </label>
                                    <textarea 
                                        id="announcement_content" 
                                        name="announcement_content" 
                                        rows="6"
                                        class="w-full px-3 py-2 border border-[var(--border)] rounded-md bg-[var(--background)] text-[var(--foreground)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent resize-vertical"
                                        placeholder="Enter detailed announcement content..."
                                    ></textarea>
                                </div>

                                <!-- Info Message -->
                                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-info-circle"></i>
                                        <span>This announcement will be sent to all students in the internship system.</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[var(--border)]">
                                    <a href="/dashboard/announcement" 
                                       class="px-4 py-2 text-sm font-medium text-[var(--muted)] hover:text-[var(--foreground)] transition">
                                        Cancel
                                    </a>
                                    <button 
                                        type="submit" 
                                        name="create_announcement"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition font-medium"
                                    >
                                        <i class="fas fa-paper-plane"></i>
                                        Send Announcement
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
