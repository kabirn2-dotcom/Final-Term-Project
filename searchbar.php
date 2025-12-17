<?php

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

  <div id="searchContainer">
    <input type="text" id="searchInput" placeholder="Search by team or date...">
  </div>

  <form id="matchForm">
    <h3 id="formTitle">Add Match</h3>
    <input type="text" id="teamA" placeholder="Team A" list="worldCupTeams" required>
    <input type="text" id="teamB" placeholder="Team B" list="worldCupTeams" required>
    <input type="datetime-local" id="matchTime" required>
    <button type="submit" id="submitBtn">Add Match</button>
    <button type="button" id="cancelBtn" class="btn btn-cancel" style="display:none;">Cancel</button>
    <div id="errorMsg"></div>
  </form>

  <datalist id="worldCupTeams"></datalist>

  <div id="matchList"></div>

  <script>
    const form=document.getElementById('matchForm');
    const matchList=document.getElementById('matchList');
    const errorMsg=document.getElementById('errorMsg');
    const submitBtn=document.getElementById('submitBtn');
    const cancelBtn=document.getElementById('cancelBtn');
    const formTitle=document.getElementById('formTitle');
    const searchInput=document.getElementById('searchInput');

    let matches=JSON.parse(localStorage.getItem('matches'))||[];
    let editingIndex=-1;
    const timers=[];

    const WORLD_CUP_TEAMS=[
      "Australia","Iran","Japan","Jordan","Qatar","Saudi Arabia",
      "South Korea","Uzbekistan","Canada","Curacao","Haiti","Mexico",
      "Panama","United States","Algeria","Cape Verde","Egypt","Ghana","Ivory Coast",
      "Morocco","Senegal","South Africa","Tunisia","Argentina","Brazil","Colombia",
      "Ecuador","Paraguay","Uruguay","New Zealand","Austria","Belgium","Croatia",
      "England","France","Germany","Netherlands","Norway","Portugal",
      "Scotland","Spain","Switzerland"
    ];

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

    const datalist=document.getElementById('worldCupTeams');
    WORLD_CUP_TEAMS.forEach(t=>{
      const opt=document.createElement('option');
      opt.value=t;
      datalist.appendChild(opt);
    });

    function displayMatches(filterTerm=""){
      timers.forEach(id=>clearInterval(id));
      timers.length=0;
      matchList.innerHTML="";
      const term=filterTerm.toLowerCase();
      matches.forEach((m,i)=>{
        const text=(m.teamA+" "+m.teamB+" "+new Date(m.time).toLocaleString()).toLowerCase();
        if(term&& !text.includes(term)) return;
        const div=document.createElement('div');
        div.className='match';
        div.innerHTML=`
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
        updateCountdown(i,m);
        const intervalId=setInterval(()=>updateCountdown(i,m),1000);
        timers.push(intervalId);
      });
      document.querySelectorAll('.btn-edit').forEach(btn=>{
        btn.onclick=()=>startEdit(+btn.dataset.index);
      });
      document.querySelectorAll('.btn-delete').forEach(btn=>{
        btn.onclick=()=>deleteMatch(+btn.dataset.index);
      });
    }

    function updateCountdown(idx,m){
      const now=new Date();
      const matchTime=new Date(m.time);
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

    function showError(txt){
      errorMsg.textContent=txt;
      errorMsg.style.display='block';
      setTimeout(()=>errorMsg.style.display='none',5000);
    }

    function startEdit(idx){
      const m=matches[idx];
      document.getElementById('teamA').value=m.teamA;
      document.getElementById('teamB').value=m.teamB;
      document.getElementById('matchTime').value=m.time.slice(0,16);
      editingIndex=idx;
      formTitle.textContent='Edit Match';
      submitBtn.textContent='Update';
      cancelBtn.style.display='inline-block';
      errorMsg.style.display='none';
    }

    function deleteMatch(idx){
      if(confirm('Delete this match?')){
        matches.splice(idx,1);
        saveAndRefresh();
      }
    }

    function cancelEdit(){
      editingIndex=-1;
      formTitle.textContent='Add Match';
      submitBtn.textContent='Add Match';
      cancelBtn.style.display='none';
      form.reset();
      errorMsg.style.display='none';
    }

    function saveAndRefresh(){
      localStorage.setItem('matches',JSON.stringify(matches));
      const term=searchInput.value.toLowerCase();
      displayMatches(term);
    }

    form.addEventListener('submit',e=>{
      e.preventDefault();
      const teamA=document.getElementById('teamA').value.trim();
      const teamB=document.getElementById('teamB').value.trim();
      const time=document.getElementById('matchTime').value;
      if(!WORLD_CUP_TEAMS.includes(teamA)) return showError(`"${teamA}" is not a qualified team.`);
      if(!WORLD_CUP_TEAMS.includes(teamB)) return showError(`"${teamB}" is not a qualified team.`);
      if(teamA===teamB) return showError('Select two different teams.');
      const logoA=TEAM_LOGOS[teamA];
      const logoB=TEAM_LOGOS[teamB];
      const newMatch={teamA,teamB,time,logoA,logoB};
      if(editingIndex>=0){
        matches[editingIndex]=newMatch;
        cancelEdit();
      }else{
        matches.push(newMatch);
      }
      saveAndRefresh();
      form.reset();
    });

    cancelBtn.addEventListener('click',cancelEdit);

    searchInput.addEventListener('input',()=>{
      const term=searchInput.value.toLowerCase();
      displayMatches(term);
    });

    if(matches.length) displayMatches();
  </script>
  <footer>© 2026 FIFA World Cup</footer>
</body>
</html>
