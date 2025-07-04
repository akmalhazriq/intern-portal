<?php
    session_start();
    
    // Check if company is logged in
    if (!isset($_SESSION['company_id'])) {
        header('Location: /login');
        exit();
    }

    // Include database connection settings
    include('../../include/dbconn.php');

    $company_id = $_SESSION['company_id'];
    $announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if (!$announcement_id) {
        header('Location: /dashboard/announcement');
        exit();
    }

    // Get company information
    $company_query = "SELECT * FROM company WHERE company_id = $company_id";
    $company_result = mysqli_query($dbconn, $company_query);
    if ($company_result && mysqli_num_rows($company_result) > 0) {
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
    } else {
        header('Location: /login');
        exit();
    }

    // Fetch the announcement and verify ownership
    $announcement_query = "SELECT * FROM announcement WHERE announcement_id = $announcement_id AND company_id = $company_id";
    $announcement_result = mysqli_query($dbconn, $announcement_query);

    if (mysqli_num_rows($announcement_result) == 0) {
        header('Location: /dashboard/announcement');
        exit();
    }

    $announcement = mysqli_fetch_assoc($announcement_result);
    $level = 3;

    // Handle form submission
    $success_message = "";
    $error_message = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_announcement'])) {
        $announcement_title = mysqli_real_escape_string($dbconn, $_POST['announcement_title']);
        $announcement_content = mysqli_real_escape_string($dbconn, $_POST['announcement_content']);

        if (!empty($announcement_title)) {
            $update_query = "UPDATE announcement SET 
                            announcement_title = '$announcement_title', 
                            announcement_content = '$announcement_content'
                            WHERE announcement_id = '$announcement_id' AND company_id = '$company_id'";

            if (mysqli_query($dbconn, $update_query)) {
                $success_message = "Announcement updated successfully!";
                // Refresh announcement data
                $announcement_result = mysqli_query($dbconn, $announcement_query);
                $announcement = mysqli_fetch_assoc($announcement_result);
            } else {
                $error_message = "Error updating announcement: " . mysqli_error($dbconn);
            }
        } else {
            $error_message = "Please fill in all required fields.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - Dashboard</title>
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
                                    if ($logo) {
                                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo) . '" alt="Company logo" class="h-full w-full object-cover" />';
                                    } else {
                                        echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                    }
                                ?>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
                                    <?php echo $full_name; ?>
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

        <div class="flex-1 ml-0 flex flex-col transition-all duration-300 ease-in-out" id="main-content">
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
            </header>

            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                <div class="max-w-4xl mx-auto space-y-6">
                    <div class="flex items-center gap-4">
                        <a href="/dashboard/announcement" class="text-[var(--muted)] hover:text-[var(--foreground)]">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Edit Announcement</h1>
                            <p class="text-[var(--muted)]">Update your announcement details.</p>
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

                    <!-- Edit Announcement Form -->
                    <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                        <div class="p-6">
                            <form method="POST" action="" class="space-y-6">
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
                                        value="<?php echo isset($_POST['announcement_title']) ? $_POST['announcement_title'] : $announcement['announcement_title']; ?>"
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
                                    ><?php echo isset($_POST['announcement_content']) ? $_POST['announcement_content'] : $announcement['announcement_content']; ?></textarea>
                                </div>

                                <!-- Target Audience (Fixed to All Students) -->
                                <div class="space-y-2">
                                    <label for="target_audience" class="text-sm font-medium">
                                        Target Audience
                                    </label>
                                    <input 
                                        type="text" 
                                        id="target_audience" 
                                        name="target_audience" 
                                        value="All Students"
                                        readonly
                                        class="w-full px-3 py-2 border border-[var(--border)] rounded-md bg-[var(--muted)] text-[var(--foreground)] cursor-not-allowed"
                                    >
                                    <p class="text-xs text-[var(--muted)]">All announcements are sent to all students in the system.</p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[var(--border)]">
                                    <a href="/dashboard/announcement" 
                                       class="px-4 py-2 text-sm font-medium text-[var(--muted)] hover:text-[var(--foreground)] transition">
                                        Cancel
                                    </a>
                                    <button 
                                        type="submit" 
                                        name="update_announcement"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition font-medium"
                                    >
                                        <i class="fas fa-save"></i>
                                        Update Announcement
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
