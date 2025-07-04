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
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT company_id, username, password, company_name, email FROM company WHERE username = '$username'";
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
    $fullName = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $level = 2;
    $sql = "SELECT user_id FROM user WHERE email = '$email'";
    $query = mysqli_query($dbconn, $sql);
    if(mysqli_num_rows($query) > 0){
        header('Location: /register?error=1');
        exit;
    }
    $sql = "INSERT INTO user (full_name, email, password, level_id) VALUES ('$fullName', '$email', '$password', '$level')";
    mysqli_query($dbconn, $sql);
    header('Location: /login?register=1');
    exit;
}

// Company Registration
if(isset($_POST['register_company'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $area = $_POST['area'];
    $state = $_POST['state'];
    $description = $_POST['description'];
    $website = $_POST['website'];

    // Check username/email
    $sql = "SELECT company_id FROM company WHERE username = '$username' OR email = '$email'";
    $query = mysqli_query($dbconn, $sql);
    if(mysqli_num_rows($query) > 0){
        header('Location: /register?error=company_exists');
        exit;
    }
    $sql = "INSERT INTO company (username, password, company_name, phone, email, area, state, description, website, verified) VALUES ('$username', '$password', '$company_name', '$phone', '$email', '$area', '$state', '$description', '$website', 0)";
    mysqli_query($dbconn, $sql);
    header('Location: /login?register=company_success');
    exit;
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
            // Sanitize name for filename
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $studentName);
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $newFileName = $safeName . '.' . $ext;
            $targetFile = $uploadDir . $newFileName;

            // Move the uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                // Update the database with the new file name
                if ($level == 2) {
                    $sql = "UPDATE user SET profile_picture = '$newFileName', full_name = '$fullName', email = '$email', phone_number = '$phoneNumber', password = '$password', area = '$area', state = '$state', campus = '$campus', course = '$course' WHERE user_id = $user_id";
                } else {
                    $sql = "UPDATE user SET profile_picture = '$newFileName', full_name = '$fullName', email = '$email', phone_number = '$phoneNumber', password = '$password' WHERE user_id = $user_id";
                }
                mysqli_query($dbconn, $sql) or die ("Error: ". mysqli_error($dbconn));
            } else {
                // Handle error
            }
        } else {
            // No new image uploaded, just update other fields
            if ($level == 2) {
                $sql = "UPDATE user SET full_name = '$fullName', email = '$email', phone_number = '$phoneNumber', password = '$password', area = '$area', state = '$state', campus = '$campus', course = '$course' WHERE user_id = $user_id";
            } else {
                $sql = "UPDATE user SET full_name = '$fullName', email = '$email', phone_number = '$phoneNumber', password = '$password' WHERE user_id = $user_id";
            }
            mysqli_query($dbconn, $sql) or die ("Error: ". mysqli_error($dbconn));
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
    if (empty($title) || empty($role) || empty($description)) {
        header('Location: /dashboard/internships/edit.php?id=' . $vacancy_id . '&error=required_fields');
        exit;
    }
    
    // Update the vacancy
    $sql = "UPDATE vacancy SET 
            title = '$title', 
            role = '$role', 
            elaun = '$elaun', 
            description = '$description', 
            vacancy_status_id = '$vacancy_status_id'
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
    
    // Validate required fields
    if (empty($title) || empty($role) || empty($description)) {
        header('Location: /dashboard/internships/create.php?error=required_fields');
        exit;
    }
    
    // Insert new vacancy using company_id from session
    $sql = "INSERT INTO vacancy (company_id, title, role, elaun, description, vacancy_status_id, created_at) 
            VALUES ('$company_id', '$title', '$role', '$elaun', '$description', '$vacancy_status_id', NOW())";
    
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

// Admin: Remove Vacancy
if (isset($_POST['remove_vacancy'])) {
    $level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
    if ($level != 1) {
        header('Location: /dashboard');
        exit;
    }
    $vacancy_id = (int)$_POST['vacancy_id'];
    // Remove the vacancy
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

// Company: Create Announcement
if(isset($_POST['create_announcement'])) {
    // Only allow companies to create announcements
    if (!isset($_SESSION['company_id'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $company_id = $_SESSION['company_id'];
    $announcement_title = mysqli_real_escape_string($dbconn, $_POST['announcement_title']);
    $announcement_content = mysqli_real_escape_string($dbconn, $_POST['announcement_content']);

    if (!empty($announcement_title)) {
        // Create announcement for all students
        $insert_query = "INSERT INTO announcement (announcement_title, announcement_content, company_id, created_at) 
                        VALUES ('$announcement_title', '$announcement_content', '$company_id', NOW())";

        if (mysqli_query($dbconn, $insert_query)) {
            header('Location: /dashboard/announcement/create.php?success=created');
            exit;
        } else {
            header('Location: /dashboard/announcement/create.php?error=create_failed');
            exit;
        }
    } else {
        header('Location: /dashboard/announcement/create.php?error=missing_fields');
        exit;
    }
}

// Company: Update Announcement
if(isset($_POST['update_announcement'])) {
    // Only allow companies to update announcements
    if (!isset($_SESSION['company_id'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $company_id = $_SESSION['company_id'];
    $announcement_id = (int)$_POST['announcement_id'];
    $announcement_title = mysqli_real_escape_string($dbconn, $_POST['announcement_title']);
    $announcement_content = mysqli_real_escape_string($dbconn, $_POST['announcement_content']);

    if (!empty($announcement_title)) {
        // Update announcement (only for announcements owned by this company)
        $update_query = "UPDATE announcement SET 
                        announcement_title = '$announcement_title', 
                        announcement_content = '$announcement_content'
                        WHERE announcement_id = '$announcement_id' AND company_id = '$company_id'";

        if (mysqli_query($dbconn, $update_query)) {
            header('Location: /dashboard/announcement/edit?id=' . $announcement_id . '&success=updated');
            exit;
        } else {
            header('Location: /dashboard/announcement/edit?id=' . $announcement_id . '&error=update_failed');
            exit;
        }
    } else {
        header('Location: /dashboard/announcement/edit?id=' . $announcement_id . '&error=missing_fields');
        exit;
    }
}

// Company: Delete Announcement
if(isset($_POST['delete_announcement'])) {
    // Only allow companies to delete announcements
    if (!isset($_SESSION['company_id'])) {
        header('Location: /dashboard');
        exit;
    }
    
    $company_id = $_SESSION['company_id'];
    $announcement_id = (int)$_POST['announcement_id'];

    // Delete announcement (only for announcements owned by this company)
    $delete_query = "DELETE FROM announcement 
                    WHERE announcement_id = '$announcement_id' AND company_id = '$company_id'";

    if (mysqli_query($dbconn, $delete_query)) {
        header('Location: /dashboard/announcement?success=deleted');
        exit;
    } else {
        header('Location: /dashboard/announcement?error=delete_failed');
        exit;
    }
}

mysqli_close($dbconn);
?>