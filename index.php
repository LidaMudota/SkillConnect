<?php
session_start();
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillMatch</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">SkillMatch</div>
            <nav>
                <ul class="header-navigation">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="search_executors.php">EXECUTORS</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a href="profile.php">PROFILE</a></li>
                        <li><a href="auth/logout.php">LOGOUT</a></li>
                    <?php else: ?>
                        <li><a href="auth/register.php">REGISTER</a></li>
                        <li><a href="auth/login.php">LOGIN</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-container">
                <h1>Welcome to <span class="highlight">SkillMatch!</span></h1>
                <p>Find the best professionals for your needs or offer your skills to others.</p>
                <div class="buttons">
                    <a href="search_executors.php"><button class="button primary">Find Executors</button></a>
                    <?php if (!isset($_SESSION['username'])): ?>
                        <a href="auth/register.php"><button class="button secondary">Register</button></a>
                        <a href="auth/login.php"><button class="button secondary">Login</button></a>
                    <?php else: ?>
                        <a href="profile.php"><button class="button secondary">Go to Profile</button></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="diagonal-section">
            <div class="content">
                <h2>Professional Services</h2>
                <p>We provide top-notch services to help you achieve your goals. Join our platform to find skilled professionals or offer your expertise to others.</p>
                <a href="search_executors.php" class="button primary">Explore Services</a>
            </div>
            <div class="image"></div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 SkillMatch. All Rights Reserved.</p>
    </footer>
</body>
</html>