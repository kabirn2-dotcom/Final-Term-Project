<?php
// =====================================
// Define World Cup group data
// Teams are listed but no matches
// have been played yet, so stats
// will start at zero
// =====================================

$groups = [
    "Group A" => ["Mexico", "South Africa", "South Korea", "Portugal"],
    "Group B" => ["Canada", "USA", "Paraguay", "Qatar"],
    "Group C" => ["Switzerland", "Brazil", "Morocco", "Haiti", "Scotland"],
    "Group D" => ["Spain", "Cape Verde", "Belgium", "Egypt"],
    "Group E" => ["Saudi Arabia", "Uruguay", "Iran", "New Zealand"],
    "Group F" => ["France", "Senegal", "Norway", "Austria", "Jordan", "Argentina", "Algeria"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<!-- Ensures proper scaling on mobile devices -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Standings</title>

<style>
/* Global page styling */
body {
    margin: 0;
    padding: 0;
    font-family: Calibri, sans-serif;
    text-align: center;

    /* Background image with overlay for readability */
    background:
        linear-gradient(rgba(255,255,255,0.65), rgba(0,0,0,0.7)),
        url("messi_bg.jpg") center/cover no-repeat;
    min-height: 100vh;
}

/* Heading styles */
h1, h4, h3 {
    color: #111;
    text-shadow: 0 2px 6px rgba(255,255,255,0.6);
}

h1 { margin-top: 25px; margin-bottom: 5px; }
h4 { margin-bottom: 30px; font-weight: normal; }

/* Container for each group */
.groupStage {
    width: 95%;
    margin: 0 auto 40px;
}

/* Group title alignment */
h3 {
    text-align: left;
    margin-left: 10px;
}

/* Standings table styling */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 8px;
    overflow: hidden;
    background: rgba(255,255,255,0.92);
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
}

/* Table header row */
th {
    background: rgb(220, 0, 0);
    color: white;
    padding: 12px;
    font-weight: bold;
}

/* Table cell styling */
td {
    padding: 12px;
    text-align: center;
}

/* Team name column formatting */
td.team {
    text-align: left;
    padding-left: 18px;
    font-weight: bold;
}

/* Zebra striping for readability */
tr:nth-child(even) { background: #f3f3f3; }
tr:nth-child(odd) { background: #e6e6e6; }

/* Hover effect for rows */
tr:hover { background: #fff0d6; }
</style>
</head>

<body>

<h1>Standings</h1>
<h4>All teams currently have 0 points — matches have not been played yet</h4>

<?php
// Loop through each group and generate a table
foreach ($groups as $groupName => $teams):
?>
<div class="groupStage">
    <h3><?= $groupName ?></h3>

    <table>
        <tr>
            <th>Rank</th>
            <th>Team</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Draws</th>
            <th>Points</th>
        </tr>

        <?php
        // Rank starts at 1 for each group
        $rank = 1;
        foreach ($teams as $team):
        ?>
        <tr>
            <td><?= $rank++ ?></td>

            <!-- htmlspecialchars prevents HTML injection -->
            <td class="team"><?= htmlspecialchars($team) ?></td>

            <!-- No matches played yet -->
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endforeach; ?>

</body>
</html>