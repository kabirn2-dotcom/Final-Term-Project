<?php
//  database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fifatracker";

// Create MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Stop if connection fails
if ($conn->connect_error) {
  die("Connection Failed");
}


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);


$input = json_decode(file_get_contents('php://input'), true);

if (is_array($input) && isset($input['action'])) {
  header('Content-Type: application/json');
  $action = $input['action'];

  // Return all matches
  if ($action == 'get_matches') {
    $sql = "SELECT MatchID, TeamA, TeamB, `Date` FROM matches ORDER BY `Date` ASC";
    $result = $conn->query($sql);
    $matches = [];
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $matches[] = $row;
      }
    }
    echo json_encode($matches);

  // Add a new match
  } else if ($action == 'add_match') {
    $teamA = $input['teamA'];
    $teamB = $input['teamB'];
    $date = str_replace('T', ' ', $input['date']) . ':00';

    $stmt = $conn->prepare("INSERT INTO matches (TeamA, TeamB, `Date`) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $teamA, $teamB, $date);
    $success = $stmt->execute();
    $matchID = $conn->insert_id;
    $stmt->close();

    echo json_encode(['success' => $success, 'MatchID' => $matchID]);

  // Update an existing match
  } else if ($action == 'update_match') {
    $matchID = $input['matchID'];
    $teamA = $input['teamA'];
    $teamB = $input['teamB'];
    $date = str_replace('T', ' ', $input['date']) . ':00';

    $stmt = $conn->prepare("UPDATE matches SET TeamA=?, TeamB=?, `Date`=? WHERE MatchID=?");
    $stmt->bind_param("sssi", $teamA, $teamB, $date, $matchID);
    $success = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $success]);

  // Delete a match
  } else if ($action == 'delete_match') {
    $matchID = $input['matchID'];

    $stmt = $conn->prepare("DELETE FROM matches WHERE MatchID=?");
    $stmt->bind_param("i", $matchID);
    $success = $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => $success]);
  }
  $conn->close();
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FIFA World Cup Match Tracker</title>
  <style>
    body{
      background:linear-gradient(rgba(255,255,255,0.45),rgba(255,255,255,0.45)),url('background.jpg') center/cover fixed;
      min-height:100vh;
      font-family:Calibri,sans-serif;
      margin:20px;
    }
    h1{
      text-align:center;
      color:#003366;
      font-size:48px;
      margin:0;
    }
    #searchContainer{
      width:340px;
      margin:15px auto 5px auto;
    }
    #searchInput{
      width:100%;
      padding:8px;
      box-sizing:border-box;
      border-radius:8px;
      border:1px solid #ccc;
    }
    #matchForm{
      background:#fff;
      padding:20px;
      border-radius:8px;
      box-shadow:0 0 5px rgba(0,0,0,.1);
      width:340px;
      margin:10px auto;
    }
    #matchForm input,#matchForm button{
      width:100%;
      padding:8px;
      margin:8px 0;
      box-sizing:border-box;
    }
    #errorMsg{
      color:#c00;
      font-size:14px;
      margin-top:5px;
      display:none;
    }
    .match{
      background:#fff;
      border:2px solid #ccc;
      border-radius:10px;
      margin:15px auto;
      padding:12px;
      width:90%;
      max-width:520px;
      text-align:center;
      transition:.3s;
      position:relative;
    }
    .match:hover{
      box-shadow:0 0 12px #007bff;
      cursor:pointer;
    }
    .upcoming{background:#fff;}
    .ongoing{background:#d4edda;}
    .completed{background:#e2e3e5;}
    .team-logo{
      height:40px;
      vertical-align:middle;
      margin:0 4px;
    }
    .btn{
      margin:5px 3px;
      padding:4px 8px;
      font-size:13px;
      cursor:pointer;
    }
    .btn-edit{background:#ffc107;color:#212529;}
    .btn-delete{background:#dc3545;color:#fff;}
    .btn-cancel{background:#6c757d;color:#fff;}
    footer{
      margin-top:auto;
      padding:20px;
      font-size:14px;
      color:black;
      text-align:center;
    }
  </style>
</head>
<body>
  <h1>Match Tracker</h1>

  <!-- Search bar -->
  <div id="searchContainer">
    <input type="text" id="searchInput" placeholder="Search by team or date...">
  </div>

  <!-- Form to add or edit a match -->
  <form id="matchForm">
    <h3 id="formTitle">Add Match</h3>
    <input type="text" id="teamA" placeholder="Team A" list="worldCupTeams" required>
    <input type="text" id="teamB" placeholder="Team B" list="worldCupTeams" required>
    <input type="datetime-local" id="matchTime" required>
    <button type="submit" id="submitBtn">Add Match</button>
    <button type="button" id="cancelBtn" class="btn btn-cancel" style="display:none;">Cancel</button>
    <div id="errorMsg"></div>
  </form>

  <!-- Team names -->
  <datalist id="worldCupTeams"></datalist>

  <!-- Where all match cards will be shown -->
  <div id="matchList"></div>

  <script>
    
    const form=document.getElementById('matchForm');
    const matchList=document.getElementById('matchList');
    const errorMsg=document.getElementById('errorMsg');
    const submitBtn=document.getElementById('submitBtn');
    const cancelBtn=document.getElementById('cancelBtn');
    const formTitle=document.getElementById('formTitle');
    const searchInput=document.getElementById('searchInput');

    // Data/stats
    let matches=[];
    let editingIndex=-1;
    const timers=[];

    // List of World Cup teams
    const WORLD_CUP_TEAMS=[
      "Australia","Iran","Japan","Jordan","Qatar","Saudi Arabia",
      "South Korea","Uzbekistan","Canada","Curacao","Haiti","Mexico",
      "Panama","United States","Algeria","Cape Verde","Egypt","Ghana","Ivory Coast",
      "Morocco","Senegal","South Africa","Tunisia","Argentina","Brazil","Colombia",
      "Ecuador","Paraguay","Uruguay","New Zealand","Austria","Belgium","Croatia",
      "England","France","Germany","Netherlands","Norway","Portugal",
      "Scotland","Spain","Switzerland"
    ];

    // Map each team to its flag image
    const TEAM_LOGOS={
      "Australia":"allFlags/Flag_of_Australia_(converted).svg",
      "Iran":"allFlags/State_flag_of_Iran_(1964–1980).png",
      "Japan":"allFlags/Flag_of_Japan.png",
      "Jordan":"allFlags/Flag_of_Jordan.png",
      "Qatar":"allFlags/Flag_of_Qatar.png",
      "Saudi Arabia":"allFlags/Flag_of_Saudi_Arabia.png",
      "South Korea":"allFlags/Flag_of_South_Korea.svg",
      "Uzbekistan":"allFlags/Flag_of_Uzbekistan.svg",
      "Canada":"allFlags/Flag_of_Canada.png",
      "Curacao":"allFlags/Flag_of_Curaçao.png",
      "Haiti":"allFlags/Flag_of_Haiti.svg",
      "Mexico":"allFlags/Flag_of_Mexico.svg",
      "Panama":"allFlags/Flag_of_Panama.png",
      "United States":"allFlags/Flag_of_the_United_States_(DDD-F-416E_specifications).png",
      "Algeria":"allFlags/Flag_of_Algeria.svg",
      "Cape Verde":"allFlags/Flag_of_Cape_Verde.png",
      "Egypt":"allFlags/Flag_of_Egypt.svg",
      "Ghana":"allFlags/Flag_of_Ghana.png",
      "Ivory Coast":"allFlags/Flag_of_Côte_d'Ivoire.png",
      "Morocco":"allFlags/Flag_of_Morocco.svg",
      "Senegal":"allFlags/Flag_of_Senegal.svg",
      "South Africa":"allFlags/Flag_of_South_Africa.png",
      "Tunisia":"allFlags/Flag_of_Tunisia.png",
      "Argentina":"allFlags/Flag_of_Argentina.svg",
      "Brazil":"allFlags/Flag_of_Brazil.png",
      "Colombia":"allFlags/Flag_of_Colombia.png",
      "Ecuador":"allFlags/Flag_of_Ecuador.svg",
      "Paraguay":"allFlags/Flag_of_Paraguay.png",
      "Uruguay":"allFlags/Flag_of_Uruguay.png",
      "New Zealand":"allFlags/Flag_of_New_Zealand.png",
      "Austria":"allFlags/Flag_of_Austria.png",
      "Belgium":"allFlags/Flag_of_Belgium.png",
      "Croatia":"allFlags/Flag-Croatia.png",
      "England":"allFlags/Flag_of_England.png",
      "France":"allFlags/france-hi.jpg",
      "Germany":"allFlags/Germany-Flag.jpg",
      "Netherlands":"allFlags/Flag_of_the_Netherlands.png",
      "Norway":"allFlags/Flag_of_Norway.png",
      "Portugal":"allFlags/Flag_of_Portugal_(official).png",
      "Scotland":"allFlags/Flag_of_Scotland.png",
      "Spain":"allFlags/Flag_of_Spain.png",
      "Switzerland":"allFlags/Flag-Switzerland.png"
    };

    // Fill datalist with team names
    const datalist=document.getElementById('worldCupTeams');
    WORLD_CUP_TEAMS.forEach(t=>{
      const opt=document.createElement('option');
      opt.value=t;
      datalist.appendChild(opt);
    });

    // Load matches from server
    async function loadMatches() {
      const response = await fetch('tracking.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'get_matches'})
      });
      matches = await response.json();
      const term = searchInput.value.toLowerCase();
      displayMatches(term);
    }

    // Render match cards with filter term
    function displayMatches(filterTerm=""){
      timers.forEach(id=>clearInterval(id));
      timers.length=0;
      matchList.innerHTML="";
      const term=filterTerm.toLowerCase();
      matches.forEach((m,i)=>{
        const logoA = TEAM_LOGOS[m.TeamA] || '';
        const logoB = TEAM_LOGOS[m.TeamB] || '';
        const text=(m.TeamA+" "+m.TeamB+" "+new Date(m.Date).toLocaleString()).toLowerCase();
        if(term&& !text.includes(term)) return;
        const div=document.createElement('div');
        div.className='match';
        div.innerHTML=`
          <img src="${logoA}" alt="${m.TeamA}" class="team-logo">
          <strong>${m.TeamA}</strong> vs <strong>${m.TeamB}</strong>
          <img src="${logoB}" alt="${m.TeamB}" class="team-logo"><br>
          <small>${new Date(m.Date).toLocaleString()}</small>
          <p id="timer${i}"></p>
          <div style="margin-top:8px;">
            <button class="btn btn-edit" data-id="${m.MatchID}">Edit</button>
            <button class="btn btn-delete" data-id="${m.MatchID}">Delete</button>
          </div>
        `;
        matchList.appendChild(div);
        updateCountdown(i,m);
        const intervalId=setInterval(()=>updateCountdown(i,m),1000);
        timers.push(intervalId);
      });
      document.querySelectorAll('.btn-edit').forEach(btn=>{
        btn.onclick=()=>startEdit(btn.dataset.id);
      });
      document.querySelectorAll('.btn-delete').forEach(btn=>{
        btn.onclick=()=>deleteMatch(btn.dataset.id);
      });
    }

    // Update countdown text and match status color
    function updateCountdown(idx,m){
      const now=new Date();
      const matchTime=new Date(m.Date);
      const diff=matchTime-now;
      const timer=document.getElementById(`timer${idx}`);
      if(!timer) return;
      const box=timer.parentElement;
      if(diff>0){
        const h=Math.floor(diff/3.6e6);
        const min=Math.floor((diff%3.6e6)/6e4);
        const sec=Math.floor((diff%6e4)/1e3);
        timer.textContent=`Kickoff in: ${h}h ${min}m ${sec}s`;
        box.className='match upcoming';
      }else if(diff>-5.4e6){
        timer.textContent='Match is Live!';
        box.className='match ongoing';
      }else{
        timer.textContent='Full Time';
        box.className='match completed';
      }
    }

    // Shows a error under the form
    function showError(txt){
      errorMsg.textContent=txt;
      errorMsg.style.display='block';
      setTimeout(()=>errorMsg.style.display='none',5000);
    }

    // Load data of a match 
    function startEdit(id){
      const m=matches.find(match => match.MatchID == id);
      document.getElementById('teamA').value=m.TeamA;
      document.getElementById('teamB').value=m.TeamB;
      document.getElementById('matchTime').value=m.Date.slice(0,16);
      editingIndex=id;
      formTitle.textContent='Edit Match';
      submitBtn.textContent='Update';
      cancelBtn.style.display='inline-block';
      errorMsg.style.display='none';
    }

    // Delete match by ID via API
    async function deleteMatch(id){
      if(confirm('Delete this match?')) {
        const response = await fetch('tracking.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({action: 'delete_match', matchID: id})
        });
        const res = await response.json();
        if (res.success) {
          loadMatches();
        }
      }
    }

    // Reset form back to "Add" mode
    function cancelEdit(){
      editingIndex=-1;
      formTitle.textContent='Add Match';
      submitBtn.textContent='Add Match';
      cancelBtn.style.display='none';
      form.reset();
      errorMsg.style.display='none';
    }

    // Handle add/update 
    form.addEventListener('submit',async e=>{
      e.preventDefault();
      const teamA=document.getElementById('teamA').value.trim();
      const teamB=document.getElementById('teamB').value.trim();
      const time=document.getElementById('matchTime').value;
      if(!WORLD_CUP_TEAMS.includes(teamA)) return showError(`"${teamA}" is not a qualified team.`);
      if(!WORLD_CUP_TEAMS.includes(teamB)) return showError(`"${teamB}" is not a qualified team.`);
      if(teamA===teamB) return showError('Select two different teams.');
      const data = {teamA, teamB, date: time, action: editingIndex >= 0 ? 'update_match' : 'add_match'};
      if (editingIndex >= 0) data.matchID = editingIndex;
      const response = await fetch('tracking.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(data)
        });
        const res = await response.json();
        if (res.success) {
          cancelEdit();
          loadMatches();
          form.reset();
        } else {
          showError('Operation Failed');
        }
    });

    
    cancelBtn.addEventListener('click',cancelEdit);

    // search filter
    searchInput.addEventListener('input',()=>{
      const term=searchInput.value.toLowerCase();
      displayMatches(term);
    });

    // Initial load
    loadMatches();
  </script>
  <footer>© 2026 FIFA World Cup</footer>
</body>
</html>
