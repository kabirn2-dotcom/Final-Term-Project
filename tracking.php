<?php

?>
<!DOCTYPE html> <!-- Lecture 2: DOCTYPE declaration -->
<html lang="en">
<head>
  <!-- Lecture 2: Head section with metadata -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FIFA World Cup Match Tracker</title> <!-- Lecture 2: Title element -->

  <!-- Lecture 4–5: Basic CSS styling (Ibrahim) -->
  <style>
    body {
        background: linear-gradient(rgba(255,255,255,0.45), rgba(255, 255, 255, 0.45)), url('background.jpg') center/cover fixed;
        min-height: 100vh;
        font-family: Calibri, sans-serif;
        margin: 20px;
    }
    h1 {
        text-align:center;
        color:#003366;
        font-size: 48px;
        margin: 0px;
    }
    #matchForm {
      background:#fff;padding:20px;border-radius:8px;
      box-shadow:0 0 5px rgba(0,0,0,.1);
      width:340px;margin:0 auto;
    }
    #matchForm input, #matchForm button {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      box-sizing:border-box;
    }
    #errorMsg{
        color: #c00;
        font-size: 14px;
        margin-top: 5px;
        display: none;
    }
    .match{
      background:#fff;border:2px solid #ccc;border-radius:10px;
      margin:15px auto;padding:12px;width:90%;max-width:520px;
      text-align:center;transition:.3s;position:relative;
    }
    /* Lecture 4: Pseudoclass hover */
    .match:hover{box-shadow:0 0 12px #007bff;cursor:pointer;}
    .upcoming   {background:#fff;}
    .ongoing    {background:#d4edda;}
    .completed  {background:#e2e3e5;}
    .team-logo{height:40px;vertical-align:middle;margin:0 4px;}
    .btn{
      margin:5px 3px;padding:4px 8px;font-size:13px;cursor:pointer;
    }
    .btn-edit{background:#ffc107;color:#212529;}
    .btn-delete{background:#dc3545;color:#fff;}
    .btn-cancel{background:#6c757d;color:#fff;}
    footer {
            margin-top: auto;
            padding: 20px;
            font-size: 14px;
            color: black;
            text-align: center;
        }
  </style>
</head>

<body>
  <!-- Lecture 2: Heading and structure (Ibrahim) -->
  <h1>Match Tracker</h1>

  <!-- Lecture 2: HTML Form for adding matches -->
  <form id="matchForm">
    <h3 id="formTitle">Add Match</h3>
    <input type="text" id="teamA" placeholder="Team A" list="worldCupTeams" required>
    <input type="text" id="teamB" placeholder="Team B" list="worldCupTeams" required>
    <input type="datetime-local" id="matchTime" required>
    <button type="submit" id="submitBtn">Add Match</button>
    <button type="button" id="cancelBtn" class="btn btn-cancel" style="display:none;">Cancel</button>
    <div id="errorMsg"></div>
  </form>

  <!-- All teams qualified for the 2026 World Cup -->
  <datalist id="worldCupTeams"></datalist>

  <!-- Lecture 2–3: Section to display list of matches (Nawal) -->
  <div id="matchList"></div>

  <!-- Lecture 6–8: JavaScript logic (Nawal) -->
  <script>
    // Lecture 6: Accessing the document object and DOM manipulation
    const form       = document.getElementById('matchForm');
    const matchList  = document.getElementById('matchList');
    const errorMsg   = document.getElementById('errorMsg');
    const submitBtn  = document.getElementById('submitBtn');
    const cancelBtn  = document.getElementById('cancelBtn');
    const formTitle  = document.getElementById('formTitle');

    // Lecture 8: Using localStorage
    let matches      = JSON.parse(localStorage.getItem('matches')) || [];
    let editingIndex = -1;          // -1 = adding, >=0 = editing that index
    const timers = [];              // stores interval IDs so we can clear them

    // World Cup teams array for validation
    const WORLD_CUP_TEAMS = [
        "Australia", "Iran", "Japan", "Jordan", "Qatar", "Saudi Arabia",
        "South Korea", "Uzbekistan", "Canada", "Curacao", "Haiti", "Mexico",
        "Panama", "United States", "Algeria", "Cape Verde", "Egypt", "Ghana", "Ivory Coast",
        "Morocco", "Senegal", "South Africa", "Tunisia", "Argentina", "Brazil", "Colombia",
        "Ecuador", "Paraguay", "Uruguay", "New Zealand", "Austria", "Belgium", "Croatia", 
        "England", "France", "Germany", "Netherlands", "Norway", "Portugal", 
        "Scotland", "Spain", "Switzerland"
    ];

    // Map of team names to their official logo URLs (mostly flags)
    const TEAM_LOGOS = {
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

    // Populate datalist dynamically (Sam)
    const datalist = document.getElementById('worldCupTeams');
    WORLD_CUP_TEAMS.forEach(t => {
      const opt = document.createElement('option');
      opt.value = t;
      datalist.appendChild(opt);
    });

    // Lecture 6: Function definition and loops (Sam)
    function displayMatches() {
      // Clear all previous timers to prevent flicker
      timers.forEach(id => clearInterval(id));
      timers.length = 0;

      matchList.innerHTML = "";
      matches.forEach((m, i) => {
        const div = document.createElement('div');
        div.className = 'match';

        // Lecture 5–6: Adding image elements (Sam)
        div.innerHTML = `
          <img src="${m.logoA}" alt="${m.teamA}" class="team-logo">
          <strong>${m.teamA}</strong> vs <strong>${m.teamB}</strong>
          <img src="${m.logoB}" alt="${m.teamB}" class="team-logo"><br>
          <small>${new Date(m.time).toLocaleString()}</small>
          <p id="timer${i}"></p>
          <div style="margin-top:8px;">
            <button class="btn btn-edit" data-index="${i}">Edit</button>
            <button class="btn btn-delete" data-index="${i}">Delete</button>
          </div>
        `;
        matchList.appendChild(div);

        // Lecture 8: Conditional logic for countdown (Nawal)
        updateCountdown(i, m);
        const intervalId = setInterval(() => updateCountdown(i, m), 1000);
        timers.push(intervalId);
      });

      // Attach edit/delete listeners
      document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.onclick = () => startEdit(+btn.dataset.index);
      });
      document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.onclick = () => deleteMatch(+btn.dataset.index);
      });
    }

    // Lecture 8: Function with date/time comparison and conditions
    function updateCountdown(idx, m) {
      const now = new Date();
      const matchTime = new Date(m.time);
      const diff = matchTime - now;
      const timer = document.getElementById(`timer${idx}`);
      const box   = timer.parentElement;

      if (diff > 0) {                               // upcoming
        const h = Math.floor(diff / 3.6e6);
        const min = Math.floor((diff % 3.6e6) / 6e4);
        const sec = Math.floor((diff % 6e4) / 1e3);
        timer.textContent = `Kickoff in: ${h}h ${min}m ${sec}s`;
        box.className = 'match upcoming';
      } else if (diff > -5.4e6) {                   // ongoing (90 min)
        timer.textContent = 'Match is Live!';
        box.className = 'match ongoing';
      } else {                                      // completed
        timer.textContent = 'Full Time';
        box.className = 'match completed';
      }
    }

    // Helper: Show temporary error
    function showError(txt) {
      errorMsg.textContent = txt;
      errorMsg.style.display = 'block';
      setTimeout(() => errorMsg.style.display = 'none', 5000);
    }

    // Edit functionality
    function startEdit(idx) {
      const m = matches[idx];
      document.getElementById('teamA').value = m.teamA;
      document.getElementById('teamB').value = m.teamB;
      document.getElementById('matchTime').value = m.time.slice(0,16);

      editingIndex = idx;
      formTitle.textContent = 'Edit Match';
      submitBtn.textContent = 'Update';
      cancelBtn.style.display = 'inline-block';
      errorMsg.style.display = 'none';
    }

    function deleteMatch(idx) {
      if (confirm('Delete this match?')) {
        matches.splice(idx, 1);
        saveAndRefresh();
      }
    }

    function cancelEdit() {
      editingIndex = -1;
      formTitle.textContent = 'Add Match';
      submitBtn.textContent = 'Add Match';
      cancelBtn.style.display = 'none';
      form.reset();
      errorMsg.style.display = 'none';
    }

    function saveAndRefresh() {
      localStorage.setItem('matches', JSON.stringify(matches));
      displayMatches();          // this also clears old timers
    }

    // Lecture 6: Event handling (Ibrahim)
    form.addEventListener('submit', e => {
      e.preventDefault();
      const teamA = document.getElementById('teamA').value.trim();
      const teamB = document.getElementById('teamB').value.trim();
      const time  = document.getElementById('matchTime').value;

      if (!WORLD_CUP_TEAMS.includes(teamA)) return showError(`"${teamA}" is not a qualified team.`);
      if (!WORLD_CUP_TEAMS.includes(teamB)) return showError(`"${teamB}" is not a qualified team.`);
      if (teamA === teamB)                 return showError('Select two different teams.');

      const logoA = TEAM_LOGOS[teamA];
      const logoB = TEAM_LOGOS[teamB];
      const newMatch = { teamA, teamB, time, logoA, logoB };

      if (editingIndex >= 0) {
        matches[editingIndex] = newMatch;
        cancelEdit();
      } else {
        matches.push(newMatch);
      }
      saveAndRefresh();
      form.reset();
    });

    cancelBtn.addEventListener('click', cancelEdit);

    // Lecture 8: Loop + condition to load stored data
    if (matches.length) displayMatches();
  </script>
    <footer>
        © 2026 FIFA World Cup
    </footer>
</body>
</html>