<?php
session_start();
require_once '../componets/conc.com.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Student Dashboard";
$basePath = '../';
$showNavbar = true;
$navItems = [
    ['label' => 'Home', 'url' => '../index.php', 'class' => ''],
    ['label' => 'Dashboard', 'url' => 'dashboard.php', 'class' => 'is-active']
];
$logoutUrl = 'logout.php';
$dashboardUrl = 'dashboard.php';
require_once '../componets/header.com.php';
?>
<section class="section">
    <div class="container">
        <div class="dashboard-header">
            <div class="level">
                <div class="level-left">
                    <div>
                        <h1 class="title is-2">Student Dashboard</h1>
                        <p class="subtitle">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="title is-4">Courses</h2>
        <?php
        // Get all courses from the database
        $query = "SELECT * FROM courses";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<div class='table-container'>";
            echo "<table class='table is-striped is-fullwidth is-hoverable'>";
            echo "<thead><tr>";
            
            // Get column names
            $fields = mysqli_fetch_fields($result);
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr></thead><tbody>";
            
            // Display course data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</tbody></table></div>";
        } else {
            echo "<div class='notification is-warning empty-state'>No courses found.</div>";
        }
        ?>
    </div>
</section>
</body>
</html>

