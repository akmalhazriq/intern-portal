<?php
// Security functions to prevent suspension

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_file_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = 5242880) {
    // Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size (5MB default)
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // Check file extension
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return false;
    }
    
    // Check MIME type
    $allowed_mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg', 
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!isset($allowed_mimes[$file_ext]) || $mime_type !== $allowed_mimes[$file_ext]) {
        return false;
    }
    
    return true;
}

function generate_safe_filename($original_name) {
    $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    return uniqid() . '_' . time() . '.' . $file_ext;
}

function prevent_directory_traversal($path) {
    // Remove any directory traversal attempts
    $path = str_replace(['../', '.\\', '..\\'], '', $path);
    return $path;
}

// Rate limiting function
function check_rate_limit($ip, $action = 'general', $max_attempts = 10, $time_window = 3600) {
    session_start();
    $key = $action . '_' . $ip;
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    $rate_data = $_SESSION['rate_limit'][$key];
    
    // Reset if time window expired
    if (time() - $rate_data['first_attempt'] > $time_window) {
        $_SESSION['rate_limit'][$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    // Check if limit exceeded
    if ($rate_data['count'] >= $max_attempts) {
        return false;
    }
    
    // Increment counter
    $_SESSION['rate_limit'][$key]['count']++;
    return true;
}

// CSRF Protection
function generate_csrf_token() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Content Security Policy
function set_security_headers() {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data: https://cdn-icons-png.flaticon.com; font-src 'self' https://cdnjs.cloudflare.com;");
}

// Note: Don't auto-call headers - let each script call them manually
?>
