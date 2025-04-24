<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="../global.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <header class="bg-[color:var(--background)] shadow-[0_1px_3px_rgba(0,0,0,0.1)] sticky z-[100] px-0 py-4 top-0">
            <div class="container flex flex-row justify-between items-center mx-auto px-4">
                <a href="/" class="flex items-center gap-2 text-xl font-semibold text-[var(--foreground)]">
                    <img src="https://www.ibec.unimas.my/images/2020/09/24/uitm.png" style="width: 24px; height: auto;" alt="FSKM Logo" class="logo-img" />
                    <span>FSKM Internship</span>
                </a>

                <nav>
                    <ul class="hidden md:flex items-center gap-12">
                        <li><a href="/" class="active text-[var(--foreground)] font-medium hover:text-[var(--primary)] ease-in-out duration-150">Home</a></li>
                        <li><a href="/company" class="text-[var(--foreground)] font-medium hover:text-[var(--primary)] duration-150">Company</a></li>
                        <li><a href="/about" class="text-[var(--foreground)] font-medium hover:text-[var(--primary)] duration-150">About</a></li>
                    </ul>
                </nav>

                <div class="hidden md:flex items-center gap-4">
                    <a href="/login" class="btn px-4 py-2 rounded-[var(--radius)] text-[var(--primary)] font-medium border border-[var(--primary)] border-solid">Login</a>
                    <a href="/register" class="btn px-4 py-2 rounded-[var(--radius)] bg-[var(--primary)] text-[var(--background)] font-medium">Register</a>
                </div>

                <button id="mobile-menu-toggle" class="block md:hidden text-var(--foreground)">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </header>

        <main>
            <section class="py-16 text-center bg-gradient-to-r from-[#f0eeff] to-[#f5f5ff]">
                <div class="container mx-auto px-4">
                    <div class="hero-content">
                        <h1 class="text-[2.5rem] text-[var(--foreground)] mb-4">Find Your Perfect Internship</h1>
                        <p class="text-lg text-[var(--muted)] mb-8">Discover opportunities that match your skills and career goals</p>
                        <div class="flex max-w-[600px] mx-auto gap-2">
                            <input type="text" placeholder="Search for internships..." class="flex-1 py-3 px-4 border border-solid border-[var(--border)] rounded-[var(--radius)]"/>
                            <button class="btn py-3 px-6 bg-[var(--primary)] text-[var(--background)] rounded-[var(--radius)] font-medium">Search</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-16">
                <div class="container mx-auto px-4">
                    <div class="flex flex-col mb-8">
                        <h2 class="text-3xl mb-2">Featured Internships</h2>
                        <p class="text-[var(--muted)] mb-4">Discover top opportunities from leading companies</p>
                        <a href="/internships" class="flex items-center self-start gap-1">
                            View all internships
                            <i class="fa-solid fa-arrow-right" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-[repeat(auto-fill, minmax(300px, 1fr))]" id="featured-internships"></div>
                </div>
            </section>
        </main>
    </body>
</html>