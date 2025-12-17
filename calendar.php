<?php
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>World Cup Calendar</title>
        <style>
            body {
                background: linear-gradient(rgba(255,255,255,0.65), rgba(0,0,0,0.65)), url('background.jpg') center/cover fixed;
                min-height: 100vh;
                font-family: Calibri, sans-serif;
                color: white;
                text-align: center;
            }

            h1 {font-size: 48px; margin: 20px 0;}
            .calendar {
                max-width: 900px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                padding: 20px;
            }

            .day {
                background: rgba(42, 57, 141, 0.85);
                border-radius: 15px;
                padding: 15px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            }
            
            .date-header {
                font-size: 24px;
                font-weight: bold;
                color: #E61D25;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #E61D25;
            }

            .match {
                background: rgba(255,255,255,0.15);
                margin: 12px 0;
                padding: 14px;
                border-radius: 10px;
                font-size: 18px;
            }

            .time {
                font-size: 15px; 
                color: #ffcccc;
                margin-bottom: 8px;
                font-weight: bold;
            }

            .team-logo {
                height: 32px;
                vertical-align: middle;
                margin: 0px;
            }

            .no-matches {
                font-style: italic;
                color: #aaa; 
                font-size: 20px;
            }

            footer {
                color: #ccc;
                padding: 20px;
                margin-top: 50px;
                font-size: 14px;  
            }

            .back {
                display: inline-block;
                margin: 15px;
                padding: 12px 28px;
                background: #E61D25;
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: bold;
            }

            .back:hover {background: #c0171f;}
        </style>
    </head>

    <body>
        <h1>FIFA World Cup Calendar</h1>
        <div id="calendarContainer">
            <p>Loading your matches...</p>
        </div>

        <script>
            const rawMatches = JSON.parse(localStorage.getItem('matches')) || [];
            const matches = rawMatches.sort((a,b) => new Date(a.time) - new Date(b.time));
            const container = document.getElementById('calendarContainer');
            container.innerHTML = '';

            if (matches.length === 0) {
                container.innerHTML = '<p class="no-matches">No matches tracked yet.</p>';
            } else {
                const matchesByDate = {};
                matches.forEach(m => {
                    const dateKey = m.time.split('T')[0];
                    if(!matchesByDate[dateKey]) matchesByDate[dateKey] = [];
                    matchesByDate[dateKey].push(m);
                });

                const calendar = document.createElement('div');
                calendar.className= 'calendar';
                
                Object.keys(matchesByDate).sort().forEach(date => {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'day';

                    const dateObj = new Date(date);
                    const options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};
                    const formattedDate = dateObj.toLocaleDateString(undefined, options);

                    dayDiv.innerHTML = `<div class="date-header">${formattedDate}</div>`;

                    matchesByDate[date].forEach(m => {
                        const matchTime = new Date(m.time).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});

                        const matchDiv = document.createElement('div');
                        matchDiv.className = 'match';
                        matchDiv.innerHTML = `
                            <div class="time">${matchTime}</div>
                            <div>
                                ${m.logoA ? `<img src="${m.logoA}" alt="${m.teamA}" class="team-logo">` : ''}
                                <span class="team">${m.teamA}</span>
                                vs
                                <span class="team">${m.teamB}</span>
                                ${m.logoB ? `<img src="${m.logoB}" alt="${m.teamB}" class="team-logo">` : ''}
                            </div>
                        `;
                        dayDiv.appendChild(matchDiv);
                    });

                    calendar.appendChild(dayDiv);
                });
                container.appendChild(calendar);
            }
        </script>
    </body>
</html>
