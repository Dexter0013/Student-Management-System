<?php
session_start();
require_once '../componets/conc.com.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Admin Dashboard";
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
                        <h1 class="title is-2">Admin Dashboard</h1>
                        <p class="subtitle">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="title is-4">Database Tables</h2>
        <?php
        // Get all tables from the database
        $query = "SHOW TABLES";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<div class='table-container'>";
            echo "<table class='table is-striped is-fullwidth is-hoverable'>";
            echo "<thead><tr><th>Table Name</th><th>Actions</th></tr></thead>";
            echo "<tbody>";
            
            while ($row = mysqli_fetch_array($result)) {
                $tableName = $row[0];
                echo "<tr>";
                echo "<td>" . htmlspecialchars($tableName) . "</td>";
                echo "<td>";
                echo "<a href='view_table.php?table=" . urlencode($tableName) . "' class='button is-small is-primary'>View Data</a> ";
                echo "<a href='manage_table.php?table=" . urlencode($tableName) . "&action=create' class='button is-small is-success'>Add</a>";
                echo "</td>";
                echo "</tr>";
            }
            
            echo "</tbody></table></div>";
        } else {
            echo "<div class='notification is-warning empty-state'>No tables found in the database.</div>";
        }
        ?>
    </div>
</section>
</body>
</html>

