<ul class="panel is-info " style="height:90vh; overflow-y:hidden; padding:10px; width:220px; position:sticky; top:0;">
    <h1 class="panel-heading">Tables</h1>

<?php
$activeTable = $activeTable ?? '';
$tablesResult = mysqli_query($conn, "SHOW TABLES");

while ($row = mysqli_fetch_row($tablesResult)) {
    $loopTable = $row[0];

    $isActiveStyle = ($loopTable === $activeTable)
        ? 'background: var(--accent-lightest);'
        : '';
?>
    <li class="panel-block is-hoverable" style=" <?= $isActiveStyle ?>">
        <a href="view_table.php?table=<?= urlencode($loopTable); ?>"
           class="panel-link">
            <?= htmlspecialchars(ucfirst($loopTable)); ?>
        </a>
    </li>
<?php } ?>
</ul>
