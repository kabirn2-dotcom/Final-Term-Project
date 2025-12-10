<?php

?>

<!-- HOMEPAGE for FIFA WORLD CUP TRACKER -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    
    <style>
        body {
            background: linear-gradient(rgba(255,255,255,0.75), rgba(0, 0, 0, 0.65)), url('background.jpg') center/cover fixed;
            min-height: 100vh;
            text-align: center;
            font-family: calibri;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1, h4 {
            margin: 0px;
        }
        h1 {
            font-size: 48px;
        }
        .options {
            text-align: center;
            display: flex;
            flex-direction: column;
            margin-top: 35px;
            width: 80%;
        }
        .menu {
            display: block;
            background-color: #ffffff;
            color: #2A398D;
            transition: all 0.3s;
            padding: 5%;
            border-radius: 25px;
            margin: 5px;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .menu:hover {
            background-color: #E61D25;
            color: white;
            transform: translateY(-3px);
            box-shadow:0 0 12px #007bff;cursor:pointer; transition: 0.3s; 

        }
        footer {
            margin-top: auto;
            padding: 20px;
            font-size: 14px;
            color: white;
        }
    </style>
</head>

<body>
    <h1>FIFA World Cup Match Tracker</h1>
    <h4>A place to keep track of all the important games happening in the World Cup!</h4>

    <!-- List of pages for user to chose, 
     each block contains a link that allows users
     to click on and travel to the various pages -->
     <div class="options">
        <a href="midtermProject.html" class="menu">Tracking Page</a>
        <a href="calendar.html" class="menu">Calendar</a>
        <a href="stats.html" class="menu">Historical Stats</a>
        <a href="standings.html" class="menu">Live Standings</a>
     </div>
     <footer>
        © 2026 FIFA World Cup
     </footer>
</body>
</html>