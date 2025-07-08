<?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        header('Location: /dashboard');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FSKM Intern System - Login</title>
    <link rel="stylesheet" href="../global.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<main class="min-h-[100svh] flex flex-col items-center justify-center">
    <div class="md:px-4 md:py-6">
        <div class="grid md:grid-cols-2 items-center gap-6 max-w-6xl">
            <div class="md:border md:rounded-lg p-6 max-w-md md:shadow max-md:mx-auto">
                <!-- Tab Buttons -->
                <div class="flex mb-6">
                    <button type="button" id="userTabBtn" class="flex-1 py-2 border-b-2 border-[var(--primary)] font-semibold" onclick="showTab('user')">Student Login</button>
                    <button type="button" id="companyTabBtn" class="flex-1 py-2 border-b-2 border-transparent font-semibold text-[var(--muted)]" onclick="showTab('company')">Company Login</button>
                </div>
                <!-- Student/Admin Login Form -->
                <form id="userForm" method="post" action="/process.php" class="space-y-6">
                    <div class="mb-10">
                                <h3 class="text-[var(--foreground)] text-3xl font-semibold text-center">Welcome Back!</h3>
                                <p class="text-[var(--muted)] text-sm mt-2 leading-relaxed text-center">Enter your credentials to access your account</p>
                            </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Email</label>
                        <input type="text" name="email" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Email">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Password</label>
                        <input type="password" name="password" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Password">
                        <?php if (isset($_GET['error'])): ?>
                            <?php if ($_GET['error'] == "1"): ?>
                                <p class="text-red-500 text-sm pt-2">Invalid email or password.</p>
                            <?php elseif ($_GET['error'] == "2"): ?>
                                <p class="text-red-500 text-sm pt-2">Invalid email or password.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($_GET['register'])): ?>
                            <?php if ($_GET['register'] == "1"): ?>
                                <p class="text-green-500 text-sm pt-2">Registration successful! Please login.</p>
                            <?php elseif ($_GET['register'] == "company_success"): ?>
                                <p class="text-green-500 text-sm pt-2">Company registration submitted! Please wait for admin verification.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="login" class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium tracking-wide rounded-lg text-[var(--background)] bg-[var(--primary)] hover:bg-[var(--primary-hover)] focus:outline-none">
                        Login&nbsp;&nbsp;<li class="fa-solid fa-arrow-right"></li>
                    </button>
                </form>
                <!-- Company Login Form -->
                <form id="companyForm" method="post" action="/process.php" class="space-y-6 hidden">
                    <div class="mb-10">
                                <h3 class="text-[var(--foreground)] text-3xl font-semibold text-center">Welcome Back!</h3>
                                <p class="text-[var(--muted)] text-sm mt-2 leading-relaxed text-center">Enter your credentials to access your account</p>
                            </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Username</label>
                        <input type="text" name="username" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Username">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Password</label>
                        <input type="password" name="password" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Password">
                        <?php if (isset($_GET['error'])): ?>
                            <?php if ($_GET['error'] == "1"): ?>
                                <p class="text-red-500 text-sm pt-2">Invalid username or password.</p>
                            <?php elseif ($_GET['error'] == "2"): ?>
                                <p class="text-red-500 text-sm pt-2">Invalid username or password.</p>
                            <?php elseif ($_GET['error'] == "not_verified"): ?>
                                <p class="text-red-500 text-sm pt-2">Your company account is pending admin verification.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="company_login" class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium tracking-wide rounded-lg text-[var(--background)] bg-[var(--primary)] hover:bg-[var(--primary-hover)] focus:outline-none">
                        Login as Company&nbsp;&nbsp;<li class="fa-solid fa-arrow-right"></li>
                    </button>
                </form>
                <p class="text-sm !mt-6 text-center text-[var(--muted)]">Don't have an account? <a href="/register" class="text-[var(--primary)] font-medium hover:underline ml-1 whitespace-nowrap">Register here</a></p>
            </div>
            <div class="max-md:mt-8 hidden md:flex">
                <img src="../src/login-image.webp" alt="" class="w-full aspect-[71/50] max-md:w-4/5 mx-auto block object-cover">
            </div>
        </div>
    </div>
</main>
<script>
    function showTab(tab) {
        const userForm = document.getElementById('userForm');
        const companyForm = document.getElementById('companyForm');
        const userTabBtn = document.getElementById('userTabBtn');
        const companyTabBtn = document.getElementById('companyTabBtn');
        
        if (tab === 'user') {
            // Show user form, hide company form
            userForm.classList.remove('hidden');
            companyForm.classList.add('hidden');
            
            // Style user tab as active
            userTabBtn.classList.add('border-[var(--primary)]');
            userTabBtn.classList.remove('border-transparent', 'text-[var(--muted)]');
            
            // Style company tab as inactive
            companyTabBtn.classList.remove('border-[var(--primary)]');
            companyTabBtn.classList.add('border-transparent', 'text-[var(--muted)]');
        } else if (tab === 'company') {
            // Show company form, hide user form
            companyForm.classList.remove('hidden');
            userForm.classList.add('hidden');
            
            // Style company tab as active
            companyTabBtn.classList.add('border-[var(--primary)]');
            companyTabBtn.classList.remove('border-transparent', 'text-[var(--muted)]');
            
            // Style user tab as inactive
            userTabBtn.classList.remove('border-[var(--primary)]');
            userTabBtn.classList.add('border-transparent', 'text-[var(--muted)]');
        }
    }
</script>
</body>
</html>