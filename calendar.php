<?php
// Connect to database
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "fifatracker";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection Failed");
}

// Load matches saved in our table
$trackedMatches = [];
$sql    = "SELECT TeamA, TeamB, `Date` FROM matches ORDER BY `Date` ASC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $trackedMatches[] = [
        "time"   => date('c', strtotime($row['Date'])),
        "teamA"  => $row['TeamA'],
        "teamB"  => $row['TeamB'],
        "source" => "tracked"
    ];
}
$conn->close();

// Call the football API
$API_KEY    = "45a91b82b36329be874aa49b274c68c4";
$apiStatus  = "offline";
$apiMatches = [];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "https://v3.football.api-sports.io/fixtures?league=1&season=2026",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        "x-apisports-key: $API_KEY"
    ],
]);

$response = curl_exec($ch);
if ($response !== false) {
    $apiStatus = "online";
}
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (!empty($data['response'])) {
        foreach ($data['response'] as $f) {
            $apiMatches[] = [
                "time"   => $f['fixture']['date'],
                "teamA"  => $f['teams']['home']['name'],
                "teamB"  => $f['teams']['away']['name'],
                "source" => "api"
            ];
        }
    }
}


$fallbackMatches = [
    ["time"=>"2026-06-11T15:00:00Z","teamA"=>"Mexico","teamB"=>"South Africa","source"=>"fallback"],
    ["time"=>"2026-06-11T22:00:00Z","teamA"=>"South Korea","teamB"=>"TBD","source"=>"fallback"],
    ["time"=>"2026-06-12T15:00:00Z","teamA"=>"Canada","teamB"=>"TBD","source"=>"fallback"],
    ["time"=>"2026-06-12T21:00:00Z","teamA"=>"USA","teamB"=>"Paraguay","source"=>"fallback"],
    ["time"=>"2026-06-13T15:00:00Z","teamA"=>"Qatar","teamB"=>"Switzerland","source"=>"fallback"],
    ["time"=>"2026-06-13T18:00:00Z","teamA"=>"Brazil","teamB"=>"Morocco","source"=>"fallback"],
    ["time"=>"2026-06-13T21:00:00Z","teamA"=>"Haiti","teamB"=>"Scotland","source"=>"fallback"],
    ["time"=>"2026-06-15T12:00:00Z","teamA"=>"Spain","teamB"=>"Cape Verde","source"=>"fallback"],
    ["time"=>"2026-06-15T15:00:00Z","teamA"=>"Belgium","teamB"=>"Egypt","source"=>"fallback"],
    ["time"=>"2026-06-15T18:00:00Z","teamA"=>"Saudi Arabia","teamB"=>"Uruguay","source"=>"fallback"],
    ["time"=>"2026-06-15T21:00:00Z","teamA"=>"Iran","teamB"=>"New Zealand","source"=>"fallback"],
    ["time"=>"2026-06-16T15:00:00Z","teamA"=>"France","teamB"=>"Senegal","source"=>"fallback"],
    ["time"=>"2026-06-16T18:00:00Z","teamA"=>"TBD","teamB"=>"Norway","source"=>"fallback"],
    ["time"=>"2026-06-16T21:00:00Z","teamA"=>"Argentina","teamB"=>"Algeria","source"=>"fallback"],
    ["time"=>"2026-06-17T00:00:00Z","teamA"=>"Austria","teamB"=>"Jordan","source"=>"fallback"],
    ["time"=>"2026-06-17T13:00:00Z","teamA"=>"Portugal","teamB"=>"TBD","source"=>"fallback"]
];

// Pick API or fallback and then merge with DB matches
$officialMatches = count($apiMatches) > 0 ? $apiMatches : $fallbackMatches;
$matches         = array_merge($trackedMatches, $officialMatches);

// Group matches by date for the calendar
$grouped = [];
foreach ($matches as $m) {
    $dateKey = substr($m['time'], 0, 10);
    $grouped[$dateKey][] = $m;
}
ksort($grouped);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>World Cup Calendar</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
    background:linear-gradient(rgba(255,255,255,0.65),rgba(0,0,0,0.7)),url('background.jpg') center/cover fixed;
    min-height:100vh;
    font-family:Calibri,sans-serif;
    color:white;
    text-align:center;
}
h1{
    font-size:48px;
    margin:25px 0;
}
.calendar{
    max-width:950px;
    margin:auto;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:20px;
    padding:20px;
}
.day{
    background:rgba(42,57,141,0.85);
    border-radius:15px;
    padding:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.6);
}
.date-header{
    font-size:22px;
    font-weight:bold;
    color:#E61D25;
    margin-bottom:12px;
    border-bottom:2px solid #E61D25;
    padding-bottom:6px;
}
.match{
    background:rgba(255,255,255,0.15);
    margin:10px 0;
    padding:12px;
    border-radius:10px;
    font-size:18px;
}
.time{
    font-size:15px;
    color:#ffcccc;
    margin-bottom:6px;
    font-weight:bold;
}
.notice{
    font-size:20px;
    margin-bottom:15px;
    color:#000;
    font-weight:bold;
}
.api-status{
    display:inline-block;
    padding:6px 14px;
    border-radius:20px;
    font-size:14px;
    font-weight:bold;
    margin-bottom:15px;
}
.api-online{
    background:#28a745;
    color:white;
}
.api-offline{
    background:#dc3545;
    color:white;
}
.tracked{border-left:5px solid #28a745;}
.api{border-left:5px solid #007bff;}
.fallback{border-left:5px solid #ffc107;}
</style>
</head>
<body>
<h1>FIFA World Cup 2026 – Match Calendar</h1>

<div class="api-status <?= $apiStatus === 'online' ? 'api-online' : 'api-offline' ?>">
    <?= $apiStatus === 'online' ? 'API Connected' : 'API Unavailable' ?>
</div>

<?php if (count($apiMatches) === 0): ?>
<div class="notice">
    Matches will show up here once the World Cup fixture is completely decided.
</div>
<?php endif; ?>

<div class="calendar">
<?php foreach ($grouped as $date => $games):
    $dateObj = new DateTime($date); ?>
    <div class="day">
        <div class="date-header">
            <?= $dateObj->format("l, F j, Y") ?>
        </div>
        <?php foreach ($games as $g):
            $time       = new DateTime($g['time']);
            $matchClass = isset($g['source']) ? $g['source'] : ''; ?>
        <div class="match <?= htmlspecialchars($matchClass) ?>">
            <div class="time"><?= $time->format("g:i A") ?></div>
            <?= htmlspecialchars($g['teamA']) ?> vs <?= htmlspecialchars($g['teamB']) ?>
        </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>
