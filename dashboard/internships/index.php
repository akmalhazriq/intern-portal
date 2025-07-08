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
                                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-[var(--primary)]">
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
                                        // Company
                                        if (isset($logo) && $logo) {
                                            echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" />';
                                        } else {
                                            echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                        }
                                    } else {
                                        // User
                                        if (isset($profile_picture) && $profile_picture) {
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
                                            echo isset($company_name) ? htmlspecialchars($company_name ?? '') : 'Company';
                                        } else {
                                            echo isset($full_name) ? htmlspecialchars($full_name ?? '') : 'User';
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
                                            if (isset($logo) && $logo) {
                                                echo '<img src="../../src/company_logo/' . htmlspecialchars($logo ?? '') . '" alt="Company logo" class="h-full w-full object-cover" />';
                                            } else {
                                                echo '<img src="https://cdn-icons-png.flaticon.com/512/3061/3061341.png" alt="Company logo" class="h-full w-full object-cover" />';
                                            }
                                        } else {
                                            // User
                                            if (isset($profile_picture) && $profile_picture) {
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
                if ($level == 3) {
            ?>
                <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                    <div class="space-y-6">
                        <div class="flex flex-row items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight">Welcome, <?php echo htmlspecialchars($company_name ?? ''); ?>!</h1>
                                <p class="text-[var(--muted)]">Manage your company profile and internships.</p>
                            </div>
                            <div>
                                <a href="/dashboard/internships/create.php"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary)] text-white rounded-md hover:bg-[var(--primary-hover)] transition">
                                    <i class="fas fa-plus"></i> New Vacancy
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php
                                $query = "SELECT v.*, c.*, s.semester_name FROM vacancy as v
                                    JOIN company as c ON v.company_id = c.company_id
                                    LEFT JOIN semester as s ON v.semester_id = s.semester_id
                                    WHERE c.company_id = $company_id";
                                $result = mysqli_query($dbconn, $query);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $vacancy_id = $row['vacancy_id'];
                                        $title = $row['title'];
                                        $company_name = $row['company_name'];
                                        $location = $row['area'] . ', ' . $row['state'];
                                        $type = isset($row['role']) ? $row['role'] : '';
                                        $salary = isset($row['elaun']) ? $row['elaun'] : '';
                                        $created_at = date('F j, Y', strtotime($row['created_at']));
                                        $description = $row['description'];
                                        $vacancy_status_id = $row['vacancy_status_id'];
                                        $semester_name = isset($row['semester_name']) ? $row['semester_name'] : 'No semester assigned';
                                        $status_class = $vacancy_status_id == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            ?>
                            <div class="group relative rounded-lg border border-[var(--border)] bg-[var(--background)] p-6 shadow-sm transition-all">
                                <!-- Dropdown Menu Trigger -->
                                <div class="inline-block text-left absolute right-7 top-7 cursor-pointer">
                                    <div class="" onclick="toggleDropdown(this)">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </div>
                                    <div class="hidden absolute right-0 mt-2 w-44 rounded-md bg-[var(--background)] shadow-lg z-30 border border-[var(--border)] dropdown-menu">
                                        <div class="py-1">
                                            <a href="/dashboard/internships/view.php?id=<?php echo $vacancy_id; ?>" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-[var(--secondary)]">
                                                <i class="fa-solid fa-eye"></i> View Vacancy
                                            </a>
                                            <a href="/dashboard/internships/edit.php?id=<?php echo $vacancy_id; ?>" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-[var(--secondary)]">
                                                <i class="fa-solid fa-pen"></i> Edit Vacancy
                                            </a>
                                            <a href="/dashboard/internships/delete.php?id=<?php echo $vacancy_id; ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fa-solid fa-trash"></i> Delete Vacancy
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <script>
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
                                        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                                            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
                                                menu.classList.add('hidden');
                                            }
                                        });
                                    });
                                </script>
                                <div class="flex h-[150px] flex-col">
                                    <div class="gap-2 flex flex-col">
                                        <h3 class="text-lg font-semibold"><?php echo $title?></h3>
                                        <p class="text-wrap break-words line-clamp-3 text-[var(--muted)] text-sm"><?php echo $description ?></p>
                                        <div class="flex items-center gap-2 text-xs text-[var(--muted)]">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?php echo $semester_name; ?></span>
                                        </div>
                                    </div>
                                    <div class="mt-auto gap-2 relative flex flex-col">
                                        <p class="text-sm text-[var(--muted)] bottom-0">
                                            Created at: <?php echo $created_at; ?>
                                        </p>
                                        <a class="flex w-full items-center justify-center" href="/dashboard/internships/view.php?id=<?php echo $vacancy_id; ?>">
                                            <button class="flex items-center justify-center gap-2 rounded-md bg-[var(--primary)] px-4 py-2 text-sm text-white hover:bg-[var(--primary-hover)] transition w-full">
                                                <i class="fa-solid fa-eye"></i> View Details
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    }
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
            ?>
                    <div class="flex-1 p-4 md:p-6 lg:p-8 overflow-auto">
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight">Find Your Next Opportunity</h1>
                                <p class="text-[var(--muted)]">Browse and apply for internships that match your skills and interests.</p>
                            </div>

                            <div class="rounded-lg border border-[var(--border)] bg-[var(--background)] shadow-sm">
                                <div class="py-8">
                                    <div class="container mx-auto px-4">
                                        <div>
                                            <h2 class="text-2xl font-medium mb-6">Find Your Perfect Internship</h2>

                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div class="col-span-1 md:col-span-2">
                                                    <div class="relative">
                                                        <i class="fas fa-search absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-[var(--muted)]"></i>
                                                        <input type="text" class="flex h-10 w-full rounded-md border border-[var(--input)] bg-[var(--background)] px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-[var(--muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm pl-10" placeholder="Search titles, companies, or skills...">
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="flex h-10 w-full items-center justify-between rounded-md border border-[var(--input)] bg-[var(--background)] px-3 py-2 text-sm ring-offset-[var(--background)] placeholder:text-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1">
                                                        <span>All Locations</span>
                                                        <i class="fa-solid fa-chevron-down h-4 w-4 text-[var(--muted)]"></i>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button type="button" class="flex h-10 w-full items-center justify-between rounded-md border border-[var(--input)] bg-[var(--background)] px-3 py-2 text-sm ring-offset-[var(--background)] placeholder:text-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1">
                                                        <span>All Types</span>
                                                        <i class="fa-solid fa-chevron-down h-4 w-4 text-[var(--muted)]"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex justify-between items-center mt-6">
                                                <div class="text-sm text-[var(--muted)]">
                                                    <?php
                                                        include_once('../../include/dbconn.php');
                                                        $query = "SELECT COUNT(*) as count FROM vacancy WHERE vacancy_status_id = 1"; // Adjust query as needed
                                                        $result = mysqli_query($dbconn, $query);
                                                        $internship_count = 0;
                                                        if ($result) {
                                                            $row = mysqli_fetch_assoc($result);
                                                            $internship_count = $row['count'];
                                                        }
                                                        echo $internship_count . ' Internships Found';
                                                    ?>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium">Sort by: </span>
                                                    <div class="relative inline-block text-left">
                                                        <button type="button" id="sort-button" class="flex h-10 w-[130px] items-center justify-between rounded-md border border-[var(--input)] bg-[var(--background)] px-3 py-2 text-sm ring-offset-[var(--background)] placeholder:text-[var(--muted)] focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                                            <span id="selected-sort">Newest</span>
                                                            <i class="fa-solid fa-chevron-down h-4 w-4 text-[var(--muted)]"></i>
                                                        </button>
                                                        
                                                        <div id="sort-dropdown" class="hidden absolute right-0 mt-2 w-[130px] rounded-md bg-[var(--background)] shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="sort-button">
                                                                <button class="flex w-full items-center px-4 py-2 text-sm text-[var(--muted)] hover:bg-[var(--secondary)]" role="menuitem" data-value="newest">
                                                                    Newest
                                                                </button>
                                                                <button class="flex w-full items-center px-4 py-2 text-sm text-[var(--muted)] hover:bg-[var(--secondary)]" role="menuitem" data-value="oldest">
                                                                    Oldest
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 mx-6">
                                                <?php
                                                    // Fetch internships from the database
                                                    $query = "SELECT v.*, c.company_name, c.area, c.state, s.semester_name FROM vacancy v 
                                                                JOIN company c ON v.company_id = c.company_id 
                                                                LEFT JOIN semester s ON v.semester_id = s.semester_id
                                                                WHERE v.vacancy_status_id = 1
                                                                ORDER BY v.vacancy_id DESC";
                                                    $result = mysqli_query($dbconn, $query);
                                                    if ($result && mysqli_num_rows($result) > 0) {
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $vacancy_id = $row['vacancy_id'];
                                                            $title = $row['title'];
                                                            $company_name = $row['company_name'];
                                                            $location = $row['area'] . ', ' . $row['state'];
                                                            $type = isset($row['role']) ? $row['role'] : '';
                                                            $salary = isset($row['elaun']) ? $row['elaun'] : '';
                                                            $created_at = date('F j, Y', strtotime($row['created_at']));
                                                            $description = $row['description'];
                                                            $vacancy_status_id = $row['vacancy_status_id'];
                                                            $semester_name = isset($row['semester_name']) ? $row['semester_name'] : 'No semester assigned';
                                                            $status_class = $vacancy_status_id == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                                ?>
                                                            <div class="bg-[var(--background)] border border-[var(--border)] rounded-lg shadow-md p-4">
                                                                <div class="flex flex-row space-between">
                                                                    <h3 class="text-lg font-semibold mb-2 truncate"><?php echo $title; ?></h3>
                                                                </div>
                                                                <p class="text-sm text-[var(--muted)] mb-1">Company: <?php echo $company_name; ?></p>
                                                                <p class="text-sm text-[var(--muted)] mb-1">Location: <?php echo $location; ?></p>
                                                                <p class="text-sm text-[var(--muted)] mb-1">Type: <?php echo $type; ?></p>
                                                                <p class="text-sm text-[var(--muted)] mb-1">Salary: <?php echo $salary; ?></p>
                                                                <p class="text-sm text-[var(--muted)] mb-1">
                                                                    <i class="fas fa-calendar-alt mr-1"></i>Semester: <?php echo $semester_name; ?>
                                                                </p>
                                                                <p class="text-sm text-[var(--muted)] mb-2">Posted on: <?php echo $created_at; ?></p>
                                                                <div class="<?php echo $status_class; ?> flex text-xs font-medium px-2 py-1 rounded mb-2">
                                                                    <?php echo $vacancy_status_id == 1 ? 'Open' : 'Closed'; ?>
                                                                </div>
                                                                <p class="text-sm text-[var(--muted)] mb-4"><?php echo substr($description, 0, 100) . '.'; ?></p>
                                                                <a href="/dashboard/internships/view.php?id=<?php echo $vacancy_id; ?>"
                                                                    class="inline-block bg-[var(--primary)] text-white px-4 py-2 rounded-md hover:bg-[var(--primary-hover)] transition-colors duration-200">View Details</a>
                                                            </div>
                                                <?php
                                                        }
                                                    } else {
                                                        echo '<p class="col-span-3 text-center text-[var(--muted)]">No internships found.</p>';
                                                    }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
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