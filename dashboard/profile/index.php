<?php
    session_start();
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
        }
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
    <link rel="stylesheet" href="../../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>My Applications</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/dashboard/profile"
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate max-w-64">
                                    <?php
                                        if ($level == 3) {
                                            echo htmlspecialchars($company_name ?? '');
                                        } else {
                                            echo htmlspecialchars($full_name ?? '');
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

            <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-y-auto">
                <div class="space-y-6">
                    <div>
                        <?php
                            if ($level == 3) {
                                echo '<h1 class="text-3xl font-bold tracking-tight">Company Profile</h1>';
                                echo '<p class="text-sm text-[var(--muted)]">Manage your company profile information and settings.</p>';
                            } else {
                                echo '<h1 class="text-3xl font-bold tracking-tight">My Profile</h1>';
                                echo '<p class="text-sm text-[var(--muted)]">Manage your profile information and settings.</p>';
                            }
                        ?>
                    </div>

                    <div class="grid gap-6 md:grid-cols-[1fr_2fr] overflow-y-auto">
                        <div class="bg-[var(--background)] rounded-lg shadow-md transition-transform duration-200">
                            <div class="p-6">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="h-24 w-24 rounded-full overflow-hidden bg-[var(--secondary)] flex items-center justify-center">
                                        <!-- Profile image -->
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
                                    <div class="space-y-1 text-center">
                                        <h2 class="text-2xl font-semibold">
                                            <?php 
                                                if ($level == 3) {
                                                    echo htmlspecialchars($company_name ?? '');
                                                } else {
                                                    echo htmlspecialchars($full_name ?? '');
                                                }
                                            ?>
                                        </h2>
                                        <p class="text-sm text-[var(--muted)]">
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
                                        </p>
                                    </div>
                                </div>

                                <hr class="my-4 border-[var(--border)]" />

                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope h-4 w-4 text-[var(--muted)]"></i>
                                        <span class="text-sm">
                                            <?php 
                                                if ($level == 3) {
                                                    echo htmlspecialchars($email_company ?? '');
                                                } else {
                                                    echo htmlspecialchars($email ?? '');
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone h-4 w-4 text-[var(--muted)]"></i>
                                        <span class="text-sm">
                                            <?php 
                                                if ($level == 3) {
                                                    echo htmlspecialchars($phone ?? '');
                                                } else {
                                                    echo htmlspecialchars($phone_number ?? '');
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <?php if ($level != 3): ?>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt h-4 w-4 text-[var(--muted)]"></i>
                                            <span class="text-sm"><?php echo htmlspecialchars($area ?? ''); ?><br/> <?php echo htmlspecialchars($state ?? ''); ?></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-graduation-cap h-4 w-4 text-[var(--muted)]"></i>
                                            <span class="text-sm"><?php echo htmlspecialchars($campus ?? ''); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt h-4 w-4 text-[var(--muted)]"></i>
                                            <span class="text-sm"><?php echo htmlspecialchars($area_company ?? ''); ?><br/> <?php echo htmlspecialchars($state_company ?? ''); ?></span>
                                        </div>
                                        <?php if ($website): ?>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-globe h-4 w-4 text-[var(--muted)]"></i>
                                                <span class="text-sm"><?php echo htmlspecialchars($website ?? ''); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="text-sm font-medium text-center text-[var(--muted)] border-b border-[var(--border)]">
                                <ul class="flex flex-wrap -mb-px" id="simple-tabs">
                                    <?php
                                        if ($level != 3) {
                                    ?>
                                        <li class="me-2">
                                            <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg text-[var(--primary)] border-[var(--primary)] active-tab" data-tab="profile-tab">Profile</a>
                                        </li>
                                    <?php
                                        } else {
                                    ?>
                                        <li class="me-2">
                                            <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg text-[var(--primary)] border-[var(--primary)] active-tab" data-tab="company-details-tab">Company Details</a>
                                        </li>
                                    <?php
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="mt-4">
                                <?php if ($level != 3): ?>
                                <div id="profile-tab" class="tab-pane">
                                    <div class="bg-[var(--background)] rounded-lg shadow-md">
                                        <div class="flex flex-col p-6">
                                            <h3 class="text-2xl font-semibold leading-none tracking-light">About Me</h3>
                                            <p class="text-sm text-[var(--muted)] space-y-1.5">Your personal bio and information</p>
                                            <form method="post" action="../../process.php" enctype="multipart/form-data">
                                                <div class="pt-5 pb-5 space-y-5">
                                                    <div>
                                                        <label for="fullName" class="text-sm font-medium mb-1 block">
                                                            <?php echo ($level == 3) ? 'Company Name' : 'Full Name'; ?>
                                                        </label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="fullName" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="<?php echo ($level == 3) ? 'Company Name' : 'Full Name'; ?>" value="<?php echo ($level == 3) ? htmlspecialchars($company_name ?? '') : htmlspecialchars($full_name ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="email" class="text-sm font-medium mb-1 block">Email</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="email" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Email" value="<?php echo ($level == 3) ? htmlspecialchars($email_company ?? '') : htmlspecialchars($email ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="phoneNumber" class="text-sm font-medium mb-1 block">Phone Number</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="phoneNumber" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Phone Number" value="<?php echo ($level == 3) ? htmlspecialchars($phone ?? '') : htmlspecialchars($phone_number ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <?php if ($level != 3): ?>
                                                    <div>
                                                        <label for="password" class="text-sm font-medium mb-1 block">Password</label>
                                                        <div class="relative flex items-center">
                                                            <input type="password" name="password" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Password" value="<?php echo isset($user) ? htmlspecialchars($user['password'] ?? '') : ''; ?>">
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    <?php if ($level != 3): ?>
                                                            <div>
                                                                <label for="area" class="text-sm font-medium mb-1 block">Area</label>
                                                                <div class="relative flex items-center">
                                                                    <input type="text" name="area" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Area" value="<?php echo htmlspecialchars($area ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label for="state" class="text-sm font-medium mb-1 block">State</label>
                                                                <div class="relative flex items-center">
                                                                    <input type="text" name="state" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="State" value="<?php echo htmlspecialchars($state ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label for="campus" class="text-sm font-medium mb-1 block">Campus</label>
                                                                <div class="relative flex items-center">
                                                                    <input type="text" name="campus" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Campus" value="<?php echo htmlspecialchars($campus ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label for="course" class="text-sm font-medium mb-1 block">Course</label>
                                                                <div class="relative flex items-center">
                                                                    <input type="text" name="course" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Course" value="<?php echo htmlspecialchars($course ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                    <?php endif; ?>
                                                    <div class="flex flex-col pt-3 pb-3 items-center justify-center">
                                                        <div class="h-40 w-40 rounded-full overflow-hidden bg-[var(--secondary)] flex items-center justify-center">
                                                            <!-- Profile image -->
                                                            <?php
                                                                if ($level == 3) {
                                                                    // Company - show logo
                                                                    if ($logo) {
                                                                        echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" id="profile-img"/>';
                                                                    } else {
                                                                        echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" id="profile-img"/>';
                                                                    }
                                                                } else {
                                                                    // User - show profile picture
                                                                    if ($profile_picture) {
                                                                        echo '<img src="../../src/profile_picture/' . htmlspecialchars($profile_picture ?? '') . '" alt="User avatar" class="h-full w-full object-cover" id="profile-img"/>';
                                                                    } else {
                                                                        if ($level == 1) {
                                                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-full w-full object-cover" id="profile-img"/>';
                                                                        } else if ($level == 2) {
                                                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-full w-full object-cover" id="profile-img"/>';
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="w-full relative flex items-center">
                                                            <input type="file" name="profile_picture" id="profile-img-input" class="w-full block text-sm border border-[var(--border)] shadow-[var(--muted)] rounded-lg outline-[var(--primary)] mt-4 file:bg-[var(--secondary)] file:border-0 file:me-4 file:py-3 file:px-4 file:text-[var(--foreground)] file:pl-7 text-[var(--foreground)] bg-[var(--input)]" accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end">
                                                    <button class="mt-4 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition-colors" type="submit" name="update_profile">
                                                        Update Profile
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($level == 3): ?>
                                <div id="company-details-tab" class="tab-pane">
                                    <div class="bg-[var(--background)] rounded-lg shadow md">
                                        <div class="flex flex-col p-6">
                                            <div class="flex flex-row gap-1">
                                                <h3 class="text-2xl font-semibold leading-none tracking-light">
                                                    <?php echo htmlspecialchars($company_name ?? ''); ?>
                                                </h3>
                                                <?php
                                                    if (isset($company) && $company['verified'] == 1) {
                                                        echo '<div class="relative group">
                                                                <!-- SVG icon -->
                                                                <svg xmlns="http://www.w3.org/2000/svg" data-tooltip-target="tooltip-right" data-tooltip-placement="right" width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor" class="text-green-500"  class="icon icon-tabler icons-tabler-filled icon-tabler-rosette-discount-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.01 2.011a3.2 3.2 0 0 1 2.113 .797l.154 .145l.698 .698a1.2 1.2 0 0 0 .71 .341l.135 .008h1a3.2 3.2 0 0 1 3.195 3.018l.005 .182v1c0 .27 .092 .533 .258 .743l.09 .1l.697 .698a3.2 3.2 0 0 1 .147 4.382l-.145 .154l-.698 .698a1.2 1.2 0 0 0 -.341 .71l-.008 .135v1a3.2 3.2 0 0 1 -3.018 3.195l-.182 .005h-1a1.2 1.2 0 0 0 -.743 .258l-.1 .09l-.698 .697a3.2 3.2 0 0 1 -4.382 .147l-.154 -.145l-.698 -.698a1.2 1.2 0 0 0 -.71 -.341l-.135 -.008h-1a3.2 3.2 0 0 1 -3.195 -3.018l-.005 -.182v-1a1.2 1.2 0 0 0 -.258 -.743l-.09 -.1l-.697 -.698a3.2 3.2 0 0 1 -.147 -4.382l.145 -.154l.698 -.698a1.2 1.2 0 0 0 .341 -.71l.008 -.135v-1l.005 -.182a3.2 3.2 0 0 1 3.013 -3.013l.182 -.005h1a1.2 1.2 0 0 0 .743 -.258l.1 -.09l.698 -.697a3.2 3.2 0 0 1 2.269 -.944zm3.697 7.282a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.32 1.497l2 2l.094 .083a1 1 0 0 0 1.32 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                                                                <!-- Tooltip -->
                                                                <div id="tooltip-right" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-[var(--muted)] rounded-lg shadow-xs opacity-0 tooltip">
                                                                    Verified
                                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                                </div>
                                                            </div>';
                                                    } else {
                                                        echo '<div class="relative group">
                                                                <!-- SVG icon -->
                                                                <svg  xmlns="http://www.w3.org/2000/svg" data-tooltip-target="tooltip-right" data-tooltip-placement="right" width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor" class="text-[var(--muted)]"  class="icon icon-tabler icons-tabler-filled icon-tabler-rosette-discount-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.01 2.011a3.2 3.2 0 0 1 2.113 .797l.154 .145l.698 .698a1.2 1.2 0 0 0 .71 .341l.135 .008h1a3.2 3.2 0 0 1 3.195 3.018l.005 .182v1c0 .27 .092 .533 .258 .743l.09 .1l.697 .698a3.2 3.2 0 0 1 .147 4.382l-.145 .154l-.698 .698a1.2 1.2 0 0 0 -.341 .71l-.008 .135v1a3.2 3.2 0 0 1 -3.018 3.195l-.182 .005h-1a1.2 1.2 0 0 0 -.743 .258l-.1 .09l-.698 .697a3.2 3.2 0 0 1 -4.382 .147l-.154 -.145l-.698 -.698a1.2 1.2 0 0 0 -.71 -.341l-.135 -.008h-1a3.2 3.2 0 0 1 -3.195 -3.018l-.005 -.182v-1a1.2 1.2 0 0 0 -.258 -.743l-.09 -.1l-.697 -.698a3.2 3.2 0 0 1 -.147 -4.382l.145 -.154l.698 -.698a1.2 1.2 0 0 0 .341 -.71l.008 -.135v-1l.005 -.182a3.2 3.2 0 0 1 3.013 -3.013l.182 -.005h1a1.2 1.2 0 0 0 .743 -.258l.1 -.09l.698 -.697a3.2 3.2 0 0 1 2.269 -.944zm3.697 7.282a1 1 0 0 0 -1.414 0l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.32 1.497l2 2l.094 .083a1 1 0 0 0 1.32 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                                                                <!-- Tooltip -->
                                                                <div id="tooltip-right" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-[var(--muted)] rounded-lg shadow-xs opacity-0 tooltip">
                                                                    Unverified
                                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                                </div>
                                                            </div>';
                                                    }   
                                                ?>
                                            </div>
                                            <p class="text-sm text-[var(--muted)] space-y-1.5">Modify your company details</p>
                                            <form action="../../process.php" method="post" enctype="multipart/form-data">
                                                <div class="pt-5 pb-5 space-y-5">
                                                    <!-- Company Banner -->
                                                    <div class="flex flex-col pt-3 pb-3 items-center justify-center">
                                                        <div class="h-40 w-full rounded-lg overflow-hidden bg-[var(--secondary)] flex items-center justify-center">
                                                            <?php
                                                                if ($banner) {
                                                                    echo '<img src="../../src/company_banner/' . htmlspecialchars($banner ?? '') . '" alt="Company Banner" class="h-full w-full object-cover" id="company-banner-img"/>';
                                                                } else {
                                                                    echo '<img src="https://picsum.photos/id/1/800/200" alt="Company Banner" class="h-full w-full object-cover" id="company-banner-img"/>';
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="w-full relative flex flex-col">
                                                            <label for="companyBanner" class="text-sm font-medium block mt-4 mb-2">Company Banner</label>
                                                            <input type="file" name="companyBanner" id="company-banner-input" class="w-full block text-sm border border-[var(--border)] shadow-[var(--muted)] rounded-lg outline-[var(--primary)] file:bg-[var(--secondary)] file:border-0 file:me-4 file:py-3 file:px-4 file:text-[var(--foreground)] file:pl-7 text-[var(--foreground)] bg-[var(--input)]" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col pt-3 pb-3 items-center justify-center">
                                                        <div class="h-40 w-40 rounded-full overflow-hidden bg-[var(--secondary)] flex items-center justify-center">
                                                            <!-- Company Logo -->
                                                            <?php
                                                                if ($logo) {
                                                                    echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" id="company-logo-img"/>';
                                                                } else {
                                                                    echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" id="company-logo-img"/>';
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="w-full relative flex flex-col">
                                                            <label for="companyLogo" class="text-sm font-medium block mt-4 mb-2">Company Logo</label>
                                                            <div class="w-full relative flex items-center">
                                                                <input type="file" name="companyLogo" id="company-logo-input" class="w-full block text-sm border border-[var(--border)] shadow-[var(--muted)] rounded-lg outline-[var(--primary)] file:bg-[var(--secondary)] file:border-0 file:me-4 file:py-3 file:px-4 file:text-[var(--foreground)] file:pl-7 text-[var(--foreground)] bg-[var(--input)]" accept="image/*">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyName" class="text-sm font-medium mb-1 block">Company Name</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="companyName" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company Name" value="<?php echo htmlspecialchars($company_name ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyEmail" class="text-sm font-medium mb-1 block">Company Email</label>
                                                        <div class="relative flex items-center">
                                                            <input type="email" name="companyEmail" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company Email" value="<?php echo htmlspecialchars($email_company ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyWebsite" class="text-sm font-medium mb-1 block">Company Website</label>
                                                        <div class="relative flex items-center">
                                                            <input type="url" name="companyWebsite" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company Website" value="<?php echo htmlspecialchars($website ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyPhoneNumber" class="text-sm font-medium mb-1 block">Company Phone Number</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="companyPhoneNumber" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company Phone Number" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyArea" class="text-sm font-medium mb-1 block">Company Area</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="companyArea" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company Area" value="<?php echo htmlspecialchars($area_company ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="companyState" class="text-sm font-medium mb-1 block">Company State</label>
                                                        <div class="relative flex items-center">
                                                            <input type="text" name="companyState" class="w-full text-sm border border-[var(--border)] shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)] text-[var(--foreground)] bg-[var(--input)]" placeholder="Company State" value="<?php echo htmlspecialchars($state_company ?? ''); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end">
                                                        <button class="mt-4 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition-colors" type="submit" name="update_company_details">
                                                            Update Company Details
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Tab switching (only for users with multiple tabs)
                                    document.querySelectorAll('#simple-tabs a[data-tab]').forEach(tab => {
                                        tab.addEventListener('click', function(e) {
                                            e.preventDefault();
                                            document.querySelectorAll('#simple-tabs a[data-tab]').forEach(t => {
                                                t.classList.remove('text-[var(--primary)]', 'border-[var(--primary)]', 'active-tab');
                                                t.classList.add('border-transparent');
                                            });
                                            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.add('hidden'));
                                            document.getElementById(this.dataset.tab).classList.remove('hidden');
                                            this.classList.add('text-[var(--primary)]', 'border-[var(--primary)]', 'active-tab');
                                            this.classList.remove('border-transparent');
                                        });
                                    });
                                });

                                // Profile image upload (for users only)
                                const profileImgInput = document.getElementById('profile-img-input');
                                if (profileImgInput) {
                                    profileImgInput.addEventListener('change', function() {
                                        const file = this.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                document.getElementById('profile-img').src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                }

                                // Company banner upload (for companies only)
                                const companyBannerInput = document.getElementById('company-banner-input');
                                if (companyBannerInput) {
                                    companyBannerInput.addEventListener('change', function() {
                                        const file = this.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                document.getElementById('company-banner-img').src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                }

                                // Company logo upload (for companies only)
                                const companyLogoInput = document.getElementById('company-logo-input');
                                if (companyLogoInput) {
                                    companyLogoInput.addEventListener('change', function() {
                                        const file = this.files[0];
                                        if (file) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                document.getElementById('company-logo-img').src = e.target.result;
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/Dashboard.js"></script>
    <script src="../path/to/flowbite/dist/flowbite.min.js"></script>
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