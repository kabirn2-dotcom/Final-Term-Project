<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Standings</title>
<style>
body {
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: Calibri, sans-serif;

    background:
        linear-gradient(rgba(255,255,255,0.70), rgba(255,255,255,0.70)),
        url("messi_bg.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
}

h1 { margin-top: 25px; margin-bottom: 5px; }
h4 { margin-top: 0; margin-bottom: 25px; font-weight: normal; }

.groupStage {
    width: 95%;
    margin: 0 auto;
}

h3 {
    text-align: left;
    margin-left: 10px;
    margin-bottom: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 6px;
    overflow: hidden;
    background: rgba(255,255,255,0.90);
    box-shadow: 0 2px 12px rgba(0,0,0,0.20);
}

th {
    background: rgb(220, 0, 0);
    color: white;
    padding: 12px;
    font-weight: bold;
    text-align: center;
}

td {
    padding: 12px;
    text-align: center;
}

tr:nth-child(even) { background-color: #f4f4f4; }
tr:nth-child(odd) { background-color: #e5e5e5; }

tr:hover { background-color: #fff0d6; }
</style>
</head>

<body>
<h1>Standings</h1>
<h4>All standings here are updated with the tracker!</h4>

<div class="groupStage">
    <h3>Group A</h3>

    <table>
        <tr>
            <th>Rank</th>
            <th>Team</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Draws</th>
            <th>Points</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Mexico</td>
            <td>2</td>
            <td>0</td>
            <td>1</td>
            <td>7</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Portugal</td>
            <td>1</td>
            <td>2</td>
            <td>0</td>
            <td>3</td>
        </tr>
    </table>
</div>
</body>
</html>
