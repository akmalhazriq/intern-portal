
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'];
$level = isset($_SESSION['level']) ? $_SESSION['level'] : null;

// Only allow students to apply
if ($level != 2) {
    header('Location: /dashboard');
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['vacancy_id']) ? $_POST['vacancy_id'] : null);

// Include database connection settings
include('../../include/dbconn.php');

// Fetch user data
$user_query = "SELECT * FROM user WHERE user_id = $user_id";
$user_result = mysqli_query($dbconn, $user_query);
if ($user_result) {
    $user = mysqli_fetch_assoc($user_result);
    $full_name = $user['full_name'];
    $email_user = $user['email'];
    $profile_picture = $user['profile_picture'];
}

// Include database connection settings
include('../../include/dbconn.php');

$query = "SELECT v.*, v.description, c.company_name, c.phone, c.email, c.area, c.state, c.website, c.verified, c.banner, c.logo FROM vacancy v 
    JOIN company c ON v.company_id = c.company_id 
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
} else {
    echo "No results found.";
}

// Include security functions
include('../../include/security.php');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $uploadDir = '../../src/resume/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Validate file upload with security checks
        $validation = validate_file_upload($_FILES['resume'], ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], 10 * 1024 * 1024); // 10MB max
        if ($validation !== true) {
            $error = 'Upload error: ' . $validation;
        } else {
            $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $_SESSION['full_name']);
            $newFileName = $safeName . '_' . date('YmdHis') . '.' . $ext;
            $targetFile = $uploadDir . $newFileName;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
                $resume_url = $newFileName;
            } else {
                $error = 'Failed to upload resume.';
            }
        }
    } else {
        $error = 'Please upload your resume.';
    }

    if (!$error) {
        // Check if already applied
        $check = mysqli_query($dbconn, "SELECT * FROM applicant WHERE user_id = '$user_id' AND vacancy_id = '$id'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'You have already applied for this position.';
        } else {
            $applied_at = date('Y-m-d H:i:s');
            $sql = "INSERT INTO applicant (user_id, vacancy_id, application_status_id, offer_status_id, resume_url, applied_at) VALUES ('$user_id', '$id', 1, 1, '$resume_url', '$applied_at')";
            if (mysqli_query($dbconn, $sql)) {
                $success = 'Application submitted successfully!';
            } else {
                $error = 'Failed to submit application.';
            }
        }
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
    <div class="min-h-[100dvh] w-full flex flex-col bg-[var(--secondary)] text-[var(--foreground)]">
        <div class="w-full flex items-center justify-between px-6 py-4 bg-[var(--card)] shadow-sm sticky top-0 z-10">
            <button onclick="window.history.back()" class="flex items-center gap-2 px-4 py-2 rounded-md text-[var(--foreground)] font-semibold transition">
                <i class="fa-solid fa-arrow-left"></i> Back
            </button>
        </div>
        <div class="flex-1 flex flex-col items-center justify-center w-full p-4">
            <div class="w-full max-w-lg bg-[var(--card)] rounded-xl shadow-lg p-8 mt-8">
                <h1 class="text-2xl font-bold mb-4 text-center">Apply for Internship</h1>
                <!-- Overview Section -->
                <div class="mb-6 p-4 rounded-lg bg-[var(--secondary)] flex flex-col gap-2">
                    <div class="flex items-center gap-3 mb-2">
                        <?php if (!empty($logo)) {
                            echo '<img src="../../src/company_logo/' . $logo . '" alt="Company Logo" class="h-10 w-10 rounded-full object-cover border-2 border-[var(--border)]">';
                        } else {
                            echo '<div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-xl text-gray-500 border-2 border-[var(--border)]">🏢</div>';
                        } ?>
                        <div>
                            <div class="font-semibold text-[var(--foreground)]"><?php echo $company_name; ?></div>
                            <div class="text-xs text-[var(--muted)]"><?php echo $area . ', ' . $state; ?></div>
                        </div>
                    </div>
                    <div class="text-base font-medium text-[var(--primary)]"><?php echo $title; ?></div>
                    <div class="text-sm text-[var(--muted)] flex flex-wrap gap-2">
                        <span><i class="fa-solid fa-user-tie mr-1"></i><?php echo $role; ?></span>
                        <span><i class="fa-solid fa-calendar mr-1"></i><?php echo $created_at; ?></span>
                        <span><i class="fa-solid fa-money-bill-wave mr-1"></i>Elaun: RM<?php echo $elaun; ?></span>
                    </div>
                    <div class="text-sm text-[var(--foreground)] mt-2 line-clamp-2"><?php echo mb_strimwidth($description, 0, 120, '...'); ?></div>
                </div>
                <?php if ($error): ?>
                    <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-center"><?php echo $error; ?></div>
                <?php elseif ($success): ?>
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-700 text-center"><?php echo $success; ?></div>
                    <a href="/dashboard/internships" class="w-full">
                        <button type="submit" class="w-full px-4 py-2 bg-[var(--primary)] text-white rounded-md font-semibold hover:bg-[var(--primary-hover)] transition">Return</button>
                    </a>
                <?php endif; ?>
                <?php if (!$success): ?>
                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="vacancy_id" value="<?php echo $id; ?>">
                    <div>
                        <label for="resume" class="block text-sm font-medium mb-2">Upload Resume (PDF, DOCX, etc.)</label>
                        <div class="w-full relative flex items-center">
                            <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" class="w-full block text-sm border border-[var(--border)] shadow-[var(--muted)] rounded-lg outline-[var(--primary)] file:bg-[var(--secondary)] file:border-0 file:me-4 file:py-3 file:px-4 file:text-[var(--foreground)] file:pl-7 text-[var(--foreground)] bg-[var(--input)]" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-[var(--primary)] text-white rounded-md font-semibold hover:bg-[var(--primary-hover)] transition">Submit Application</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>