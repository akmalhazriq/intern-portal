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
    <title>FSKM Intern System - Register</title>
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
                    <button type="button" id="studentTabBtn" class="flex-1 py-2 border-b-2 border-[var(--primary)] font-semibold" onclick="showTab('student')">Student Registration</button>
                    <button type="button" id="companyTabBtn" class="flex-1 py-2 border-b-2 border-transparent font-semibold text-[var(--muted)]" onclick="showTab('company')">Company Registration</button>
                </div>
                <!-- Student Registration Form -->
                <form id="studentForm" method="post" action="/process.php" class="space-y-6">
                    <div class="mb-10">
                        <h3 class="text-[var(--foreground)] text-3xl font-semibold text-center">Create Your Account</h3>
                        <p class="text-[var(--muted)] text-sm mt-2 leading-relaxed text-center">Join our community and unlock opportunities</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Full Name</label>
                        <div class="relative flex items-center">
                            <input type="text" name="name" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Name">
                            <i class="fa-solid fa-user absolute right-4 text-[var(--muted)]"></i>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Email</label>
                        <div class="relative flex items-center">
                            <input type="email" name="email" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Email">
                            <i class="fa-solid fa-envelope absolute right-4 text-[var(--muted)]"></i>
                        </div>
                        <?php if (isset($_GET['error']) && $_GET['error'] == "1") { ?>
                            <p class="text-red-500 text-sm pt-2">The account with this email already exists.</p>
                        <?php } ?>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Password</label>
                        <div class="relative flex items-center">
                            <input type="password" name="password" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Password">
                            <i class="fa-solid fa-lock absolute right-4 text-[var(--muted)]"></i>
                        </div>
                    </div>
                    <input type="hidden" name="level" value="2">
                    <div class="!mt-10">
                        <button type="submit" name="register" class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium tracking-wide rounded-lg text-[var(--background)] bg-[var(--primary)] hover:bg-[var(--primary-hover)] focus:outline-none">
                            Create Account&nbsp;&nbsp;<li class="fa-solid fa-arrow-right"></li>
                        </button>
                        <p class="text-sm !mt-6 text-center text-[var(--muted)]">Already have an account? <a href="/login" class="text-[var(--primary)] font-medium hover:underline ml-1 whitespace-nowrap">Login</a></p>
                    </div>
                </form>
                <!-- Company Registration Form -->
                <form id="companyForm" method="post" action="/process.php" class="space-y-6 hidden">
                    <div class="mb-10">
                        <h3 class="text-[var(--foreground)] text-3xl font-semibold text-center">Company Registration</h3>
                        <p class="text-[var(--muted)] text-sm mt-2 leading-relaxed text-center">Submit your company details for access</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Username</label>
                        <input type="text" name="username" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Username">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Password</label>
                        <input type="password" name="password" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Password">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Company Name</label>
                        <input type="text" name="company_name" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Company Name">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Phone</label>
                        <input type="text" name="phone" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Phone">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Email</label>
                        <input type="email" name="email" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Email">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Area</label>
                        <input type="text" name="area" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Area">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">State</label>
                        <input type="text" name="state" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="State">
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Description</label>
                        <textarea name="description" required class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Description"></textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium mb-2 block">Website</label>
                        <input type="text" name="website" class="w-full text-sm border shadow-[var(--muted)] pl-4 pr-10 py-3 rounded-lg outline-[var(--primary)]" placeholder="Website">
                    </div>
                    <div class="!mt-10">
                        <button type="submit" name="register_company" class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium tracking-wide rounded-lg text-[var(--background)] bg-[var(--primary)] hover:bg-[var(--primary-hover)] focus:outline-none">
                            Submit Company Registration&nbsp;&nbsp;<li class="fa-solid fa-arrow-right"></li>
                        </button>
                        <p class="text-sm !mt-6 text-center text-[var(--muted)]">Already have an account? <a href="/login" class="text-[var(--primary)] font-medium hover:underline ml-1 whitespace-nowrap">Login</a></p>
                    </div>
                </form>
            </div>
            <div class="max-md:mt-8 hidden md:flex">
                <img src="../src/login-image.webp" alt="" class="w-full aspect-[71/50] max-md:w-4/5 mx-auto block object-cover">
            </div>
        </div>
    </div>
</main>
<script>
    function showTab(tab) {
        document.getElementById('studentForm').classList.toggle('hidden', tab !== 'student');
        document.getElementById('companyForm').classList.toggle('hidden', tab !== 'company');
        document.getElementById('studentTabBtn').classList.toggle('border-[var(--primary)]', tab === 'student');
        document.getElementById('studentTabBtn').classList.toggle('text-[var(--muted)]', tab !== 'student');
        document.getElementById('companyTabBtn').classList.toggle('border-[var(--primary)]', tab === 'company');
        document.getElementById('companyTabBtn').classList.toggle('text-[var(--muted)]', tab !== 'company');
    }
</script>
</body>
</html>