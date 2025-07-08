<?php
// Inialize session
session_start();

// Include database connection settings
include('./include/dbconn.php');

// Student/Admin Login
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT user_id, email, password, level_id, full_name FROM user WHERE email = '$email'";
    $query = mysqli_query($dbconn, $sql);
    if(mysqli_num_rows($query) == 0){
        header('Location: /login?error=1');
        exit;
    }
    $r = mysqli_fetch_assoc($query);
    if($r['password'] !== $password){
        header('Location: /login?error=2');
        exit;
    }
    $_SESSION['user_id'] = $r['user_id'];
    $_SESSION['email'] = $r['email'];
    $_SESSION['full_name'] = $r['full_name'];
    $_SESSION['level'] = $r['level_id'];
    $_SESSION['user_type'] = 'user';
    header('Location: /dashboard');
    exit;
}

// Company Login
if(isset($_POST['company_login'])){
    $username = mysqli_real_escape_string($dbconn, trim($_POST['username']));
    $password = $_POST['password'];
    
    $sql = "SELECT company_id, username, password, company_name, email, verified FROM company WHERE username = '$username'";
    $query = mysqli_query($dbconn, $sql);
    
    if(mysqli_num_rows($query) == 0){
        header('Location: /login?error=1');
        exit;
    }
    
    $r = mysqli_fetch_assoc($query);
    
    // Check if password matches (handle both hashed and plain text for backward compatibility)
    $password_valid = false;
    if (password_verify($password, $r['password'])) {
        // New hashed password
        $password_valid = true;
    } elseif ($r['password'] === $password) {
        // Old plain text password (for backward compatibility)
        $password_valid = true;
        // Optionally update to hashed password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE company SET password = '$hashed_password' WHERE company_id = " . $r['company_id'];
        mysqli_query($dbconn, $update_sql);
    }
    
    if(!$password_valid){
        header('Location: /login?error=2');
        exit;
    }
    
    // Check if company is verified
    if($r['verified'] == 0){
        header('Location: /login?error=not_verified');
        exit;
    }
    
    $_SESSION['company_id'] = $r['company_id'];
    $_SESSION['username'] = $r['username'];
    $_SESSION['company_name'] = $r['company_name'];
    $_SESSION['email'] = $r['email'];
    $_SESSION['level'] = 3;
    $_SESSION['user_type'] = 'company';
    header('Location: /dashboard');
    exit;
}

// Student Registration
if(isset($_POST['register'])){
    $fullName = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $level = 2;
    
    // Use prepared statement to check for existing email
    $stmt = $dbconn->prepare("SELECT user_id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        header('Location: /register?error=1');
        exit;
    }
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statement to insert new user
    $stmt = $dbconn->prepare("INSERT INTO user (full_name, email, password, level_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $fullName, $email, $hashed_password, $level);
    $stmt->execute();
    
    header('Location: /login?register=1');
    exit;
}

// Company Registration
if(isset($_POST['register_company'])){
    $username = mysqli_real_escape_string($dbconn, trim($_POST['username']));
    $password = mysqli_real_escape_string($dbconn, $_POST['password']);
    $company_name = mysqli_real_escape_string($dbconn, trim($_POST['company_name']));
    $email = mysqli_real_escape_string($dbconn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($dbconn, trim($_POST['phone']));
    $area = mysqli_real_escape_string($dbconn, trim($_POST['area']));
    $state = mysqli_real_escape_string($dbconn, trim($_POST['state']));
    $description = mysqli_real_escape_string($dbconn, trim($_POST['description']));
    $website = mysqli_real_escape_string($dbconn, trim($_POST['website']));

    // Basic validation
    if(empty($username) || empty($password) || empty($company_name) || empty($email) || empty($phone)) {
        header('Location: /register?error=missing_fields');
        exit;
    }

    // Validate email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /register?error=invalid_email');
        exit;
    }

    // Check if username or email already exists
    $sql = "SELECT company_id FROM company WHERE username = '$username' OR email = '$email'";
    $query = mysqli_query($dbconn, $sql);
    if(mysqli_num_rows($query) > 0){
        header('Location: /register?error=company_exists');
        exit;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert company record with verified = 0
    $sql = "INSERT INTO company (username, password, company_name, phone, email, area, state, description, website, verified) VALUES ('$username', '$hashed_password', '$company_name', '$phone', '$email', '$area', '$state', '$description', '$website', 0)";
    
    if(mysqli_query($dbconn, $sql)) {
        header('Location: /login?register=company_success');
        exit;
    } else {
        header('Location: /register?error=registration_failed');
        exit;
    }
}

if(isset($_POST['update_profile'])) {
    $level = isset($_SESSION['level'])? $_SESSION['level'] : null;
    
    // Only handle user profiles (level 1 & 2) - companies use update_company_details
    if ($level == 1 || $level == 2) {
        $user_id = $_SESSION['user_id'];
        $fullName = $_POST['fullName'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $password = $_POST['password'];
        
        if ($level == 2) {
            // Student additional fields
            $area = $_POST['area'];
            $state = $_POST['state'];
            $campus = $_POST['campus'];
            $course = $_POST['course'];
        }

        $studentName = $fullName;

        // Handle profile picture upload
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $uploadDir = 'src/profile_picture/';
            
            // Validate file upload
            $validation = validateFileUpload($_FILES['profile_picture'], ['jpg', 'jpeg', 'png', 'gif'], 5 * 1024 * 1024); // 5MB max
            if ($validation !== true) {
                die("Upload error: " . $validation);
            }
            
            // Sanitize name for filename
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $studentName);
            $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            $newFileName = $safeName . '.' . $ext;
            $targetFile = $uploadDir . $newFileName;

            // Move the uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                // Update the database with the new file name using prepared statements
                if ($level == 2) {
                    $stmt = $dbconn->prepare("UPDATE user SET profile_picture = ?, full_name = ?, email = ?, phone_number = ?, password = ?, area = ?, state = ?, campus = ?, course = ? WHERE user_id = ?");
                    $stmt->bind_param("sssssssssi", $newFileName, $fullName, $email, $phoneNumber, $password, $area, $state, $campus, $course, $user_id);
                } else {
                    $stmt = $dbconn->prepare("UPDATE user SET profile_picture = ?, full_name = ?, email = ?, phone_number = ?, password = ? WHERE user_id = ?");
                    $stmt->bind_param("sssssi", $newFileName, $fullName, $email, $phoneNumber, $password, $user_id);
                }
                $stmt->execute();
            } else {
                die("Error: Failed to upload file");
            }
        } else {
            // No new image uploaded, just update other fields using prepared statements
            if ($level == 2) {
                $stmt = $dbconn->prepare("UPDATE user SET full_name = ?, email = ?, phone_number = ?, password = ?, area = ?, state = ?, campus = ?, course = ? WHERE user_id = ?");
                $stmt->bind_param("ssssssssi", $fullName, $email, $phoneNumber, $password, $area, $state, $campus, $course, $user_id);
            } else {
                $stmt = $dbconn->prepare("UPDATE user SET full_name = ?, email = ?, phone_number = ?, password = ? WHERE user_id = ?");
                $stmt->bind_param("ssssi", $fullName, $email, $phoneNumber, $password, $user_id);
            }
            $stmt->execute();
        }
        
        // Update session variables
        $_SESSION['full_name'] = $fullName;
        $_SESSION['email'] = $email;
        
        // Redirect
        header('Location: /dashboard/profile');
        exit;
    }
}

if (isset($_POST['update_company_details'])) {
	$companyName = $_POST['companyName'];
	$companyEmail = $_POST['companyEmail'];
	$companyWebsite = $_POST['companyWebsite'];
	$companyPhone = $_POST['companyPhoneNumber'];
	$companyArea = $_POST['companyArea'];
	$companyState = $_POST['companyState'];
	
	// Use company_id for companies, not user_id
	$companyId = $_SESSION['company_id'];

	$bannerUploaded = isset($_FILES['companyBanner']) && $_FILES['companyBanner']['error'] == 0;
	$logoUploaded = isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] == 0;

	// Prepare file names if needed
	$safeCompanyName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $companyName);
	$bannerFile = '';
	$logoFile = '';

	if ($bannerUploaded) {
		$uploadDirBanner = 'src/company_banner/';
		$extBanner = pathinfo($_FILES['companyBanner']['name'], PATHINFO_EXTENSION);
		$bannerFile = $safeCompanyName . '_banner.' . $extBanner;
		$targetFileBanner = $uploadDirBanner . $bannerFile;
		move_uploaded_file($_FILES['companyBanner']['tmp_name'], $targetFileBanner);
	}
	if ($logoUploaded) {
		$uploadDirLogo = 'src/company_logo/';
		$extLogo = pathinfo($_FILES['companyLogo']['name'], PATHINFO_EXTENSION);
		$logoFile = $safeCompanyName . '_logo.' . $extLogo;
		$targetFileLogo = $uploadDirLogo . $logoFile;
		move_uploaded_file($_FILES['companyLogo']['tmp_name'], $targetFileLogo);
	}

	// Update company profile using company_id
	$set = "company_name = '$companyName', email = '$companyEmail', website = '$companyWebsite', phone = '$companyPhone', area = '$companyArea', state = '$companyState'";
	if ($bannerUploaded) $set .= ", banner = '$bannerFile'";
	if ($logoUploaded) $set .= ", logo = '$logoFile'";
	$sql = "UPDATE company SET $set WHERE company_id = $companyId";
	mysqli_query($dbconn, $sql) or die ("Error: ". mysqli_error($dbconn));
	
	// Update session variables if they changed
	$_SESSION['company_name'] = $companyName;
	$_SESSION['email'] = $companyEmail;
	
	// Redirect
	header('Location: /dashboard/profile');
	exit;
}

if(isset($_POST['update_internship'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $company_id = $_SESSION['company_id'];
    
    // Only allow companies to update internships
    if ($level != 3) {
        header('Location: /dashboard');
        exit;
    }
    
    $vacancy_id = (int)$_POST['vacancy_id'];
    $title = mysqli_real_escape_string($dbconn, $_POST['title']);
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);
    $elaun = mysqli_real_escape_string($dbconn, $_POST['elaun']);
    $description = mysqli_real_escape_string($dbconn, $_POST['description']);
    $vacancy_status_id = (int)$_POST['vacancy_status_id'];
    $semester_id = isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : null;
    
    // Verify that the company owns this vacancy
    $verify_query = "SELECT v.vacancy_id FROM vacancy v 
					WHERE v.vacancy_id = '$vacancy_id' AND v.company_id = '$company_id'";
    $verify_result = mysqli_query($dbconn, $verify_query);
    
    if (mysqli_num_rows($verify_result) == 0) {
        // Company doesn't own this vacancy
        header('Location: /dashboard/internships?error=unauthorized');
        exit;
    }
    
    // Validate required fields
    if (empty($title) || empty($role) || empty($description) || empty($semester_id)) {
        header('Location: /dashboard/internships/edit.php?id=' . $vacancy_id . '&error=required_fields');
        exit;
    }
    
    // Update the vacancy
    $sql = "UPDATE vacancy SET 
            title = '$title', 
            role = '$role', 
            elaun = '$elaun', 
            description = '$description', 
            vacancy_status_id = '$vacancy_status_id',
            semester_id = '$semester_id'
            WHERE vacancy_id = '$vacancy_id'";
    
    if (mysqli_query($dbconn, $sql)) {
        header('Location: /dashboard/internships/edit.php?id=' . $vacancy_id . '&success=updated');
        exit;
    } else {
        header('Location: /dashboard/internships/edit.php?id=' . $vacancy_id . '&error=update_failed');
        exit;
    }
}

if(isset($_POST['create_internship'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $company_id = $_SESSION['company_id'];
    
    // Only allow companies to create internships
    if ($level != 3) {
        header('Location: /dashboard');
        exit;
    }
    
    $title = mysqli_real_escape_string($dbconn, $_POST['title']);
    $role = mysqli_real_escape_string($dbconn, $_POST['role']);
    $elaun = mysqli_real_escape_string($dbconn, $_POST['elaun']);
    $description = mysqli_real_escape_string($dbconn, $_POST['description']);
    $vacancy_status_id = isset($_POST['vacancy_status_id']) ? (int)$_POST['vacancy_status_id'] : 1;
    $semester_id = isset($_POST['semester_id']) ? (int)$_POST['semester_id'] : null;
    
    // Validate required fields
    if (empty($title) || empty($role) || empty($description) || empty($semester_id)) {
        header('Location: /dashboard/internships/create.php?error=required_fields');
        exit;
    }
    
    // Insert new vacancy using company_id from session
    $sql = "INSERT INTO vacancy (company_id, semester_id, title, role, elaun, description, vacancy_status_id, created_at) 
            VALUES ('$company_id', '$semester_id', '$title', '$role', '$elaun', '$description', '$vacancy_status_id', NOW())";
    
    if (mysqli_query($dbconn, $sql)) {
        $new_vacancy_id = mysqli_insert_id($dbconn);
        header('Location: /dashboard/internships');
        exit;
    } else {
        header('Location: /dashboard/internships/create.php?error=create_failed');
        exit;
    }
}

if(isset($_POST['delete_internship'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $company_id = $_SESSION['company_id'];
    
    // Only allow companies to delete internships
    if ($level != 3) {
        header('Location: /dashboard');
        exit;
    }
    
    $vacancy_id = (int)$_POST['vacancy_id'];
    
    // Verify that the company owns this vacancy
    $verify_query = "SELECT v.vacancy_id FROM vacancy v 
					WHERE v.vacancy_id = '$vacancy_id' AND v.company_id = '$company_id'";
    $verify_result = mysqli_query($dbconn, $verify_query);
    
    if (mysqli_num_rows($verify_result) == 0) {
        // Company doesn't own this vacancy
        header('Location: /dashboard/internships?error=unauthorized');
        exit;
    }
    
    // Check if there are any applications for this vacancy
    $app_check = "SELECT COUNT(*) as app_count FROM applicant WHERE vacancy_id = '$vacancy_id'";
    $app_result = mysqli_query($dbconn, $app_check);
    $app_data = mysqli_fetch_assoc($app_result);
    
    if ($app_data['app_count'] > 0) {
        // Cannot delete vacancy with applications, just set status to closed
        $sql = "UPDATE vacancy SET vacancy_status_id = 2 WHERE vacancy_id = '$vacancy_id'";
        mysqli_query($dbconn, $sql);
        header('Location: /dashboard/internships?info=vacancy_closed');
        exit;
    } else {
        // No applications, safe to delete
        $sql = "DELETE FROM vacancy WHERE vacancy_id = '$vacancy_id'";
        if (mysqli_query($dbconn, $sql)) {
            header('Location: /dashboard/internships?success=deleted');
            exit;
        } else {
            header('Location: /dashboard/internships?error=delete_failed');
            exit;
        }
    }
}

if(isset($_POST['update_application_status'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $company_id = $_SESSION['company_id'];
    
    // Only allow companies to update application status
    if ($level != 3) {
        header('Location: /dashboard');
        exit;
    }
    
    $applicant_id = (int)$_POST['applicant_id'];
    $vacancy_id = (int)$_POST['vacancy_id'];
    $application_status_id = (int)$_POST['application_status_id'];
    
    // Verify that the company owns this vacancy
    $verify_query = "SELECT v.vacancy_id FROM vacancy v 
					WHERE v.vacancy_id = '$vacancy_id' AND v.company_id = '$company_id'";
    $verify_result = mysqli_query($dbconn, $verify_query);
    
    if (mysqli_num_rows($verify_result) == 0) {
        header('Location: /dashboard/applications?error=unauthorized');
        exit;
    }
    
    // Get company_id and status names for announcement
    $company_query = "SELECT c.company_id, v.title, ast.status_name 
                     FROM vacancy v 
                     JOIN company c ON v.company_id = c.company_id 
                     JOIN application_status ast ON ast.application_status_id = '$application_status_id'
                     WHERE v.vacancy_id = '$vacancy_id'";
    $company_result = mysqli_query($dbconn, $company_query);
    $company_data = mysqli_fetch_assoc($company_result);
    $company_id = $company_data['company_id'];
    $vacancy_title = $company_data['title'];
    $status_name = $company_data['status_name'];

    // Update the application status
    $sql = "UPDATE applicant SET 
            application_status_id = '$application_status_id'
            WHERE user_id = '$applicant_id' AND vacancy_id = '$vacancy_id'";
    
    if (mysqli_query($dbconn, $sql)) {
        // Note: Individual status notifications removed since announcement table doesn't support targeting specific users

        // If status is changed from "Offered" to something else, reset offer status to "Pending"
        if ($application_status_id != 4) {
            $reset_offer_sql = "UPDATE applicant SET offer_status_id = 1 WHERE user_id = '$applicant_id' AND vacancy_id = '$vacancy_id'";
            mysqli_query($dbconn, $reset_offer_sql);
        }
        header('Location: /dashboard/applications?success=status_updated');
        exit;
    } else {
        header('Location: /dashboard/applications?error=update_failed');
        exit;
    }
}

if(isset($_POST['update_offer_status'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $company_id = $_SESSION['company_id'];
    
    // Only allow companies to update offer status
    if ($level != 3) {
        header('Location: /dashboard');
        exit;
    }
    
    $applicant_id = (int)$_POST['applicant_id'];
    $vacancy_id = (int)$_POST['vacancy_id'];
    $offer_status_id = (int)$_POST['offer_status_id'];
      // Verify that the company owns this vacancy
    $verify_query = "SELECT v.vacancy_id FROM vacancy v 
					WHERE v.vacancy_id = '$vacancy_id' AND v.company_id = '$company_id'";
    $verify_result = mysqli_query($dbconn, $verify_query);
    
    if (mysqli_num_rows($verify_result) == 0) {
        header('Location: /dashboard/applications?error=unauthorized');
        exit;
    }
    
    // Verify that the application exists
    $app_check = "SELECT user_id FROM applicant 
					WHERE user_id = '$applicant_id' AND vacancy_id = '$vacancy_id'";
    $app_result = mysqli_query($dbconn, $app_check);
    
    if (mysqli_num_rows($app_result) == 0) {
        header('Location: /dashboard/applications?error=unauthorized');
        exit;
    }
    
    // Get company_id and offer status names for announcement
    $offer_query = "SELECT c.company_id, v.title, ost.status_name 
                   FROM vacancy v 
                   JOIN company c ON v.company_id = c.company_id 
                   JOIN offer_status ost ON ost.offer_status_id = '$offer_status_id'
                   WHERE v.vacancy_id = '$vacancy_id'";
    $offer_result = mysqli_query($dbconn, $offer_query);
    $offer_data = mysqli_fetch_assoc($offer_result);
    $company_id = $offer_data['company_id'];
    $vacancy_title = $offer_data['title'];
    $offer_status_name = $offer_data['status_name'];

    // Update the offer status
    $sql = "UPDATE applicant SET 
            offer_status_id = '$offer_status_id'
            WHERE user_id = '$applicant_id' AND vacancy_id = '$vacancy_id'";
    
    if (mysqli_query($dbconn, $sql)) {
        // Note: Individual offer status notifications removed since announcement table doesn't support targeting specific users

        header('Location: /dashboard/applications?success=offer_updated');
        exit;
    } else {
        header('Location: /dashboard/applications?error=update_failed');
        exit;
    }
}

if(isset($_POST['respond_to_offer'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    $user_id = $_SESSION['user_id'];
    
    // Only allow students to respond to offers
    if ($level != 2) {
        header('Location: /dashboard');
        exit;
    }
    
    $vacancy_id = (int)$_POST['vacancy_id'];
    $offer_response = (int)$_POST['offer_response']; // 2 = Accepted, 3 = Declined
    
    // Verify that this is the student's application and offer is pending
    $verify_query = "SELECT application_status_id, offer_status_id FROM applicant 
						WHERE user_id = '$user_id' AND vacancy_id = '$vacancy_id' 
						AND application_status_id = 4 AND offer_status_id = 1";
    $verify_result = mysqli_query($dbconn, $verify_query);
    
    if (mysqli_num_rows($verify_result) == 0) {
        header('Location: /dashboard/status?error=unauthorized');
        exit;
    }
    
    // Validate response (2 = Accepted, 3 = Declined)
    if ($offer_response != 2 && $offer_response != 3) {
        header('Location: /dashboard/status?error=unauthorized');
        exit;
    }
    
    // Update the offer status
    $sql = "UPDATE applicant SET 
            offer_status_id = '$offer_response'
            WHERE user_id = '$user_id' AND vacancy_id = '$vacancy_id'";
    
    if (mysqli_query($dbconn, $sql)) {
        $success_message = ($offer_response == 2) ? 'offer_accepted' : 'offer_declined';
        header('Location: /dashboard/status?success=' . $success_message);
        exit;
    } else {
        header('Location: /dashboard/status?error=update_failed');
        exit;
    }
}

// Admin: Verify Company
if (isset($_POST['verify_company'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $company_id = (int)$_POST['company_id'];
    $sql = "UPDATE company SET verified = 1 WHERE company_id = '$company_id'";
    if (mysqli_query($dbconn, $sql)) {
        header('Location: /dashboard/manage/company?success=verified');
        exit;
    } else {
        header('Location: /dashboard/manage/company?error=verify_failed');
        exit;
    }
}

// Admin: Unverify Company
if (isset($_POST['unverify_company'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $company_id = (int)$_POST['company_id'];
    $sql = "UPDATE company SET verified = 0 WHERE company_id = '$company_id'";
    if (mysqli_query($dbconn, $sql)) {
        header('Location: /dashboard/manage/company?success=unverified');
        exit;
    } else {
        header('Location: /dashboard/manage/company?error=unverify_failed');
        exit;
    }
}

// Admin: Remove Company (and its user)
if (isset($_POST['remove_company'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $company_id = (int)$_POST['company_id'];
    $user_id = (int)$_POST['user_id'];
    // Delete all announcements for this company
    $sql0 = "DELETE FROM announcement WHERE company_id = '$company_id'";
    mysqli_query($dbconn, $sql0);
    // Delete all applicants for all vacancies of this company
    $vacancy_ids = [];
    $vacancy_res = mysqli_query($dbconn, "SELECT vacancy_id FROM vacancy WHERE company_id = '$company_id'");
    while ($v = mysqli_fetch_assoc($vacancy_res)) {
        $vacancy_ids[] = $v['vacancy_id'];
    }
    if (!empty($vacancy_ids)) {
        $ids = implode(',', array_map('intval', $vacancy_ids));
        mysqli_query($dbconn, "DELETE FROM applicant WHERE vacancy_id IN ($ids)");
    }
    // Delete all vacancies for this company
    $sql1 = "DELETE FROM vacancy WHERE company_id = '$company_id'";
    mysqli_query($dbconn, $sql1);
    // Remove company
    $sql2 = "DELETE FROM company WHERE company_id = '$company_id'";
    // Remove user
    $sql3 = "DELETE FROM user WHERE user_id = '$user_id'";
    $ok2 = mysqli_query($dbconn, $sql2);
    $ok3 = mysqli_query($dbconn, $sql3);
    if ($ok2 && $ok3) {
        header('Location: /dashboard/manage/company?success=removed');
        exit;
    } else {
        header('Location: /dashboard/manage/company?error=remove_failed');
        exit;
    }
}

// Admin: Change Vacancy Status
if (isset($_POST['change_vacancy_status'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $vacancy_id = (int)$_POST['vacancy_id'];
    $new_status = (int)$_POST['new_status'];
    
    // Validate status (1 = Open, 2 = Closed)
    if ($new_status != 1 && $new_status != 2) {
        header('Location: /dashboard/manage/vacancy?error=invalid_status');
        exit;
    }
    
    // Update the vacancy status
    $sql = "UPDATE vacancy SET vacancy_status_id = '$new_status' WHERE vacancy_id = '$vacancy_id'";
    if (mysqli_query($dbconn, $sql)) {
        $status_name = ($new_status == 1) ? 'opened' : 'closed';
        header("Location: /dashboard/manage/vacancy?success=status_changed&status=$status_name");
        exit;
    } else {
        header('Location: /dashboard/manage/vacancy?error=status_change_failed');
        exit;
    }
}

// Admin: Remove Vacancy
if (isset($_POST['remove_vacancy'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $vacancy_id = (int)$_POST['vacancy_id'];
    
    // Check if vacancy has any applicants
    $check_sql = "SELECT COUNT(*) as applicant_count FROM applicant WHERE vacancy_id = '$vacancy_id'";
    $check_result = mysqli_query($dbconn, $check_sql);
    $check_row = mysqli_fetch_assoc($check_result);
    
    if ($check_row['applicant_count'] > 0) {
        // Cannot delete vacancy with applicants
        header('Location: /dashboard/manage/vacancy?error=has_applicants');
        exit;
    }
    
    // Remove the vacancy (only if no applicants)
    $sql = "DELETE FROM vacancy WHERE vacancy_id = '$vacancy_id'";
    if (mysqli_query($dbconn, $sql)) {
        header('Location: /dashboard/manage/vacancy?success=removed');
        exit;
    } else {
        header('Location: /dashboard/manage/vacancy?error=remove_failed');
        exit;
    }
}

// Admin: Remove User
if (isset($_POST['remove_user'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $user_id = (int)$_POST['user_id'];
    // Remove the user (will also remove company if company HR)
    $sql = "DELETE FROM user WHERE user_id = '$user_id'";
    if (mysqli_query($dbconn, $sql)) {
        header('Location: /dashboard/manage/users?success=removed');
        exit;
    } else {
        header('Location: /dashboard/manage/users?error=remove_failed');
        exit;
    }
}

mysqli_close($dbconn);
?>