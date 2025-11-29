<?php
session_start();
require_once '../componets/conc.com.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Get table name and action
$tableName = isset($_GET['table']) ? mysqli_real_escape_string($conn, $_GET['table']) : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$recordId = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($tableName)) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'create') {
            // Create new record
            $columns = [];
            $values = [];
            foreach ($_POST as $key => $value) {
                if ($key != 'action' && $key != 'table') {
                    $columns[] = "`" . mysqli_real_escape_string($conn, $key) . "`";
                    $values[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
                }
            }
            $query = "INSERT INTO `$tableName` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Record created successfully!";
                header("Location: view_table.php?table=" . urlencode($tableName));
                exit();
            } else {
                $_SESSION['error'] = "Error creating record: " . mysqli_error($conn);
            }
        } elseif ($action == 'update') {
            // Update existing record
            $recordId = mysqli_real_escape_string($conn, $_POST['id']);
            $updates = [];
            foreach ($_POST as $key => $value) {
                if ($key != 'action' && $key != 'table' && $key != 'id') {
                    $updates[] = "`" . mysqli_real_escape_string($conn, $key) . "` = '" . mysqli_real_escape_string($conn, $value) . "'";
                }
            }
            
            // Get primary key
            $primaryKey = '';
            $result = mysqli_query($conn, "SHOW KEYS FROM `$tableName` WHERE Key_name = 'PRIMARY'");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $primaryKey = $row['Column_name'];
            }
            
            if ($primaryKey) {
                $query = "UPDATE `$tableName` SET " . implode(", ", $updates) . " WHERE `$primaryKey` = '$recordId'";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['success'] = "Record updated successfully!";
                    header("Location: view_table.php?table=" . urlencode($tableName));
                    exit();
                } else {
                    $_SESSION['error'] = "Error updating record: " . mysqli_error($conn);
                }
            }
        } elseif ($action == 'delete') {
            // Delete record
            $recordId = mysqli_real_escape_string($conn, $_POST['id']);
            
            // Get primary key
            $primaryKey = '';
            $result = mysqli_query($conn, "SHOW KEYS FROM `$tableName` WHERE Key_name = 'PRIMARY'");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $primaryKey = $row['Column_name'];
            }
            
            if ($primaryKey) {
                $query = "DELETE FROM `$tableName` WHERE `$primaryKey` = '$recordId'";
                if (mysqli_query($conn, $query)) {
                    $_SESSION['success'] = "Record deleted successfully!";
                    header("Location: view_table.php?table=" . urlencode($tableName));
                    exit();
                } else {
                    $_SESSION['error'] = "Error deleting record: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Get table structure
$query = "DESCRIBE `$tableName`";
$result = mysqli_query($conn, $query);
$columns = [];
$primaryKey = '';

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row;
        if ($row['Key'] == 'PRI') {
            $primaryKey = $row['Field'];
        }
    }
}

// Get record data for edit
$recordData = [];
if ($action == 'edit' && !empty($recordId) && $primaryKey) {
    $query = "SELECT * FROM `$tableName` WHERE `$primaryKey` = '$recordId' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $recordData = $row;
    }
}

$pageTitle = ucfirst($action) . " Record - " . htmlspecialchars($tableName);
$basePath = '../';
$showNavbar = true;
$navItems = [
    ['label' => 'Home', 'url' => '../index.php', 'class' => ''],
    ['label' => 'Dashboard', 'url' => 'dashboard.php', 'class' => ''],
    ['label' => 'View Table', 'url' => 'view_table.php?table=' . urlencode($tableName), 'class' => '']
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
                    <h1 class="title is-2"><?php echo ucfirst($action); ?> Record - <?php echo htmlspecialchars($tableName); ?></h1>
                </div>
                <div class="level-right">
                    <a href="view_table.php?table=<?php echo urlencode($tableName); ?>" class="button is-link">Back to Table</a>
                </div>
            </div>
        </div>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='notification is-danger mb-2'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<div class='notification is-success mb-2'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
        ?>

        <?php if ($action == 'delete'): ?>
            <div class="box">
                <h2 class="title is-4">Confirm Delete</h2>
                <p>Are you sure you want to delete this record?</p>
                <form method="POST" action="manage_table.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($tableName); ?>">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($recordId); ?>">
                    <div class="field is-grouped mt-2">
                        <div class="control">
                            <button type="submit" class="button is-danger">Delete</button>
                        </div>
                        <div class="control">
                            <a href="view_table.php?table=<?php echo urlencode($tableName); ?>" class="button is-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="box">
                <form method="POST" action="manage_table.php">
                    <input type="hidden" name="action" value="<?php echo htmlspecialchars($action); ?>">
                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($tableName); ?>">
                    <?php if ($action == 'edit' && $primaryKey): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($recordId); ?>">
                    <?php endif; ?>

                    <?php foreach ($columns as $column): ?>
                        <?php if ($column['Key'] != 'PRI' || $action == 'edit'): ?>
                            <div class="field">
                                <label class="label" for="<?php echo htmlspecialchars($column['Field']); ?>">
                                    <?php echo htmlspecialchars($column['Field']); ?>
                                    <?php if ($column['Null'] == 'NO' && $column['Key'] != 'PRI'): ?>
                                        <span class="has-text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                <div class="control">
                                    <?php
                                    $value = isset($recordData[$column['Field']]) ? $recordData[$column['Field']] : '';
                                    $fieldName = $column['Field'];
                                    $isRequired = ($column['Null'] == 'NO' && $column['Key'] != 'PRI');
                                    
                                    if (strpos($column['Type'], 'text') !== false || strpos($column['Type'], 'varchar') !== false): ?>
                                        <input class="input" type="text" 
                                               id="<?php echo htmlspecialchars($fieldName); ?>" 
                                               name="<?php echo htmlspecialchars($fieldName); ?>" 
                                               value="<?php echo htmlspecialchars($value); ?>"
                                               <?php echo $isRequired ? 'required' : ''; ?>>
                                    <?php elseif (strpos($column['Type'], 'int') !== false): ?>
                                        <input class="input" type="number" 
                                               id="<?php echo htmlspecialchars($fieldName); ?>" 
                                               name="<?php echo htmlspecialchars($fieldName); ?>" 
                                               value="<?php echo htmlspecialchars($value); ?>"
                                               <?php echo $isRequired ? 'required' : ''; ?>>
                                    <?php elseif (strpos($column['Type'], 'date') !== false): ?>
                                        <input class="input" type="date" 
                                               id="<?php echo htmlspecialchars($fieldName); ?>" 
                                               name="<?php echo htmlspecialchars($fieldName); ?>" 
                                               value="<?php echo htmlspecialchars($value); ?>"
                                               <?php echo $isRequired ? 'required' : ''; ?>>
                                    <?php elseif (strpos($column['Type'], 'time') !== false): ?>
                                        <input class="input" type="time" 
                                               id="<?php echo htmlspecialchars($fieldName); ?>" 
                                               name="<?php echo htmlspecialchars($fieldName); ?>" 
                                               value="<?php echo htmlspecialchars($value); ?>"
                                               <?php echo $isRequired ? 'required' : ''; ?>>
                                    <?php else: ?>
                                        <input class="input" type="text" 
                                               id="<?php echo htmlspecialchars($fieldName); ?>" 
                                               name="<?php echo htmlspecialchars($fieldName); ?>" 
                                               value="<?php echo htmlspecialchars($value); ?>"
                                               <?php echo $isRequired ? 'required' : ''; ?>>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="field is-grouped mt-2">
                        <div class="control">
                            <button type="submit" class="button is-primary">
                                <?php echo $action == 'edit' ? 'Update' : 'Create'; ?> Record
                            </button>
                        </div>
                        <div class="control">
                            <a href="view_table.php?table=<?php echo urlencode($tableName); ?>" class="button is-light">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>
</body>
</html>


