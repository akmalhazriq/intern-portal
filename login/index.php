<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <main>
            <div class="auth-container">
                <div class="auth-logo">
                    <a href="/" class="logo">

                    </a>
                </div>

                <div class="auth-card">
                    <div class="auth-header">
                        <h1>Welcome Back</h1>
                        <p>Enter your credentials to access your account</p>
                    </div>

                    <form action="" class="auth-form" id="login-form">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-input" name="email" required placeholder="Enter your email" />
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" class="form-input" name="password" required placeholder="Enter your password" />
                            <a href="/forgot-password" class="auth-link" style="display: block; text-align: right; margin-top: 0.5rem; font-size: 0.875rem;">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn-auth">Sign In</button>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>