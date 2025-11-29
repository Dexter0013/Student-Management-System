<?php
$pageTitle = "Home - Navigation";
$basePath = '';
$showNavbar = true;
$navItems = [
    ['label' => 'Home', 'url' => 'index.php', 'class' => 'is-active']
];
require_once 'componets/header.com.php';
?>
<section class="section">
    <div class="container">
        <h1 class="title is-1 has-text-centered">Welcome</h1>
        <p class="subtitle is-4 has-text-centered">Please select your section:</p>
        <div class="buttons is-centered">
            <a href="admin/" class="button is-primary is-large">Admin</a>
            <a href="student/" class="button is-info is-large">Student</a>
        </div>
    </div>
</section>
</body>
</html>

