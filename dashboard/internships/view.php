<?php
    session_start();
    
    // Check if user or company is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
        
        // Include database connection settings
        include('../../include/dbconn.php');
        
        // Fetch user data
        $query = "SELECT * FROM user WHERE user_id = $user_id";
        $result = mysqli_query($dbconn, $query);
        if ($result) {
            $user = mysqli_fetch_assoc($result);
            $full_name = $user['full_name'];
            $email_user = $user['email'];
            $area_user = $user['area'];
            $state_user = $user['state'];
            $phone_number = $user['phone_number'];
            $campus = $user['campus'];
            $course = $user['course'];
            $profile_picture = $user['profile_picture'];
        }
    } elseif (isset($_SESSION['company_id'])) {
        $company_id = $_SESSION['company_id'];
        $level = 3;
        
        // Include database connection settings
        include('../../include/dbconn.php');
        
        // Get company information
        $company_query = "SELECT * FROM company WHERE company_id = $company_id";
        $company_result = mysqli_query($dbconn, $company_query);
        if ($company_result && mysqli_num_rows($company_result) > 0) {
            $company = mysqli_fetch_assoc($company_result);
            $company_name_logged = $company['company_name'];
            $phone_logged = $company['phone'];
            $email_company_logged = $company['email'];
            $area_company_logged = $company['area'];
            $state_company_logged = $company['state'];
            $description_logged = $company['description'];
            $website_logged = $company['website'];
            $verified_logged = $company['verified'];
            $banner_logged = $company['banner'];
            $logo_logged = $company['logo'];
            
            // For UI consistency
            $full_name = $company_name_logged;
        }
    } else {
        header('Location: /login');
        exit;
    }

    $id = $_GET['id'];

    $query = "SELECT v.*, v.description, c.company_name, c.phone, c.email, c.area, c.state, c.website, c.verified, c.banner, c.logo, s.semester_name, s.start_date, s.end_date FROM vacancy v 
        JOIN company c ON v.company_id = c.company_id 
        LEFT JOIN semester s ON v.semester_id = s.semester_id
        WHERE v.vacancy_id = '$id'";
    $result = mysqli_query($dbconn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $company_name = $row['company_name'];
        $phone = $row['phone'];
        $email = $row['email'];
        $area = $row['area'];
        $state = $row['state'];
        $website = $row['website'];
        $verified = $row['verified'];
        $banner = $row['banner'];
        $logo = $row['logo'];

        $title = $row['title'];
        $role = $row['role'];
        $elaun = $row['elaun'];
        $created_at = date('F j, Y', strtotime($row['created_at']));
        $description = $row['description'];
        $vacancy_status_id = $row['vacancy_status_id'];
        $semester_name = isset($row['semester_name']) ? $row['semester_name'] : 'No semester assigned';
        $semester_period = '';
        if (isset($row['start_date']) && isset($row['end_date'])) {
            $semester_period = date('M Y', strtotime($row['start_date'])) . ' - ' . date('M Y', strtotime($row['end_date']));
        }
    } else {
        echo "No results found.";
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
    <div class="min-h-[100dvh] w-full flex flex-col bg-[var(--secondary)] text-[var(--foreground)]">
        <div class="w-full flex items-center justify-between px-6 py-4 bg-[var(--card)] shadow-sm sticky top-0 z-10">
            <button onclick="window.history.back()" class="flex items-center gap-2 px-4 py-2 rounded-md text-[var(--foreground)] font-semibold transition">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <?php if ($level == 2) { ?>
                <a href="/dashboard/internships/apply.php?id=<?php echo $id; ?>" class="flex items-center">
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-md bg-[var(--primary)] text-white font-semibold hover:bg-[var(--primary-hover)] transition">
                        <i class="fa-solid fa-paper-plane"></i> Apply Now
                    </button>
                </a>
            <?php } ?>
        </div>
        <div class="flex-1 flex flex-col items-center justify-start w-full p-0 md:p-8">
            <div class="w-full max-w-5xl bg-[var(--card)] rounded-none md:rounded-xl shadow-lg overflow-hidden mt-0 md:mt-6">
                <!-- Banner -->
                <div class="h-64 w-full bg-gray-200 relative flex items-end justify-center">
                    <?php if ($banner) {
                        echo '<img src="../../src/company_banner/' . $banner . '" alt="Company Banner" class="absolute inset-0 w-full h-full object-cover">';
                    } else {
                        echo '<img src="https://picsum.photos/id/1/800/200' . $banner . '" alt="Company Banner" class="absolute inset-0 w-full h-full object-cover">';
                    } ?>
                    <!-- Overlay for better text visibility -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <!-- Profile picture over banner -->
                    <div class="relative z-10 -mb-12 flex flex-col items-center">
                        <?php if ($logo) {
                            echo '<img src="../../src/company_logo/' . $logo . '" alt="Company Logo" class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover">';
                        } else {
                            if ($level == 1) {
                                echo '<img src="https://cdn-icons-png.flaticon.com/512/6024/6024190.png" alt="User avatar" class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover" id="company-logo-img"/>';
                            } else if ($level == 2) {
                                echo '<img src="https://cdn-icons-png.flaticon.com/512/201/201818.png" alt="User avatar" class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover" id="company-logo-img"/>';
                            } else if ($level == 3) {
                                echo '<img src="   https://cdn-icons-png.flaticon.com/512/3061/3061341.png " alt="User avatar" class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover" id="company-logo-img"/>';
                            }
                        } ?>
                    </div>
                </div>
                <!-- Company Info below banner/profile -->
                <div class="flex flex-col items-center mt-16 mb-8">
                    <div class="flex items-center gap-2">
                        <h2 class="text-2xl font-bold text-[var(--foreground)] text-center"><?php echo $company_name; ?></h2>
                        <?php if ($verified) {
                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800"><i class="fa-solid fa-check-circle mr-1"></i>Verified</span>';
                        } else {
                            echo '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-600"><i class="fa-solid fa-times-circle mr-1"></i>Unverified</span>';
                        } ?>
                    </div>
                    <div class="text-sm text-[var(--muted)] text-center"><?php echo $area . ', ' . $state; ?></div>
                    <div class="text-sm text-[var(--muted)] mt-1 text-center"><i class="fa-solid fa-globe mr-1"></i> <a href="<?php echo $website; ?>" target="_blank" class="hover:underline"><?php echo $website; ?></a></div>
                </div>
                <!-- Main Content -->
                <div class="p-6 md:p-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold mb-2"><?php echo $title; ?></h1>
                            <div class="flex flex-wrap gap-3 text-base text-[var(--muted)] mb-2">
                                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-tie"></i> <?php echo $role; ?></span>
                                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-calendar-alt"></i> <?php echo $semester_name; ?></span>
                                <?php if ($semester_period): ?>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-clock"></i> <?php echo $semester_period; ?></span>
                                <?php endif; ?>
                                <span class="inline-flex items-center gap-1"><i class="fa-solid fa-calendar"></i> Posted: <?php echo $created_at; ?></span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <span class="inline-flex items-center px-4 py-2 rounded-full bg-[var(--primary-light)] text-[var(--primary)] font-semibold text-xl">
                                <i class="fa-solid fa-money-bill-wave mr-2"></i> Elaun: RM<?php echo $elaun; ?>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded text-sm font-semibold
                                <?php
                                    if ($vacancy_status_id == 1) echo 'bg-green-100 text-green-800';
                                    elseif ($vacancy_status_id == 2) echo 'bg-red-100 text-red-800';
                                ?>">
                                <?php
                                    if ($vacancy_status_id == 1) echo 'Open';
                                    elseif ($vacancy_status_id == 2) echo 'Closed';
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-3">Description</h3>
                        <p class="text-lg text-[var(--foreground)] leading-relaxed whitespace-pre-line"><?php echo $description; ?></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-[var(--secondary)] rounded-lg p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-envelope"></i> <span><?php echo $email; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-phone"></i> <span><?php echo $phone; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-globe"></i> <a href="<?php echo $website; ?>" target="_blank" class="hover:underline"><?php echo $website; ?></a></div>
                        </div>
                        <div class="bg-[var(--secondary)] rounded-lg p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-building"></i> <span><?php echo $company_name; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-location-dot"></i> <span><?php echo $area . ', ' . $state; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-id-badge"></i> <span>Company Verified: <?php echo $verified ? 'Yes' : 'No'; ?></span></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-[var(--secondary)] rounded-lg p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-briefcase"></i> <span>Vacancy ID: <?php echo $id; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-user-tie"></i> <span>Role: <?php echo $role; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-calendar-plus"></i> <span>Posted: <?php echo $created_at; ?></span></div>
                        </div>
                        <div class="bg-[var(--secondary)] rounded-lg p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-money-bill-wave"></i> <span>Elaun: RM<?php echo $elaun; ?></span></div>
                            <div class="flex items-center gap-2 text-[var(--muted)]"><i class="fa-solid fa-circle-info"></i> <span>Status: <?php
                                if ($vacancy_status_id == 1) echo 'Open';
                                elseif ($vacancy_status_id == 2) echo 'Closed';
                            ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>