<?php

// Inialize session
session_start();

// Delete certain session
unset($_SESSION['user_id']);
unset($_SESSION['email']);
unset($_SESSION['full_name']);
unset($_SESSION['level_id']);

session_unset(); 

// Delete all session variables
session_destroy();

// Jump to login page
header('Location: /login');

?>
