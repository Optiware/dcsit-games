<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campus IT | Monitoring</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-text: #f8fafc;
            --primary: #3b82f6;
            --bg-main: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #334155;
            --border: #e2e8f0;
            --stockage: #10b981;
            --reseau: #6366f1;
        }

        body { font-family: 'Roboto', sans-serif; margin: 0; background-color: var(--bg-main); display: flex; height: 100vh; color: var(--text-dark); }

        /* SIDEBAR (Menu Latéral) */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); display: flex; flex-direction: column; flex-shrink: 0; }
        .brand { padding: 20px; font-size: 1.5rem; font-weight: 700; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: 10px; }
        .brand span { color: var(--primary); }
        .menu { list-style: none; padding: 0; margin: 20px 0; }
        .menu li { padding: 0; margin-bottom: 5px; }
        .menu button {
            background: none; border: none; width: 100%; text-align: left; padding: 15px 25px;
            color: #94a3b8; font-size: 1rem; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 10px;
        }
        .menu button:hover, .menu button.active { background-color: #334155; color: white; border-left: 4px solid var(--primary); }
        .user-info { margin-top: auto; padding: 20px; border-top: 1px solid #334155; font-size: 0.85rem; color: #94a3b8; }

        /* MAIN CONTENT */
        .main-content { flex-grow: 1; overflow-y: auto; padding: 30px; }
        .header { margin-bottom: 30px; }
        .header h2 { margin: 0; font-size: 1.8rem; font-weight: 500; }
        .header p { color: #64748b; margin-top: 5px; }

        /* CARDS & CONTAINERS */
        .card { background: var(--card-bg); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 20px; animation: fadeIn 0.5s ease; display: none; }
        .card.visible { display: block; }
        .card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: #0f172a; border-bottom: 2px solid var(--bg-main); padding-bottom: 15px; }

        /* TABLES */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; background-color: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 700; border-bottom: 1px solid var(--border); }
        td { padding: 15px 12px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        tr:last-child td { border: none; }

        /* UTILITAIRES COMPARATIFS (Visualisation) */
        .bar-container { background-color: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden; width: 100px; display: inline-block; vertical-align: middle; margin-right: 10px; }
        .bar-fill { height: 100%; border-radius: 4px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-stock { background-color: #d1fae5; color: #065f46; }
        .badge-res { background-color: #e0e7ff; color: #3730a3; }

        .comparison-row { display: flex; align-items: center; gap: 15px; }
        .stat-box { flex: 1; text-align: center; padding: 10px; background: #f8fafc; border-radius: 6px; }
        .stat-val { font-size: 1.2rem; font-weight: 700; display: block; }
        .stat-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        <span>⚡</span> Campus IT
    </div>
    <ul class="menu">
        <li><button class="active" onclick="showTab('tab-apps', this)">📊 Top Applications</button></li>
        <li><button onclick="showTab('tab-evol', this)">📈 Évolution Mensuelle</button></li>
        <li><button onclick="showTab('tab-compare', this)">⚖️ Comparateur</button></li>
    </ul>
    <div class="user-info">
        Service Informatique<br>
        Session: Admin
    </div>
</aside>

<main class="main-content">
    <div class="header">
        <h2>Tableau de Bord</h2>
        <p>Analyse des ressources (Janvier - Juin 2025)</p>
    </div>

    <div id="tab-apps" class="card visible">
        <div class="card-title">Top 5 Applications (Consommation Totale)</div>
        <div id="content-apps">Chargement...</div>
    </div>

    <div id="tab-evol" class="card">
        <div class="card-title">Évolution Globale du Campus</div>
        <div id="content-evol">Chargement...</div>
    </div>

    <div id="tab-compare" class="card">
        <div class="card-title">Comparaison : Stockage vs Réseau</div>
        <p style="font-size:0.9rem; color:#64748b; margin-bottom:20px;">Analyse comparative des volumes consommés mois par mois.</p>
        <div id="content-compare">Chargement...</div>
    </div>
</main>

<script>
    // Gestion des onglets
    function showTab(id, btn) {
        document.querySelectorAll('.card').forEach(c => c.classList.remove('visible'));
        document.getElementById(id).classList.add('visible');
        document.querySelectorAll('.menu button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    // Chargement des données au démarrage
    document.addEventListener('DOMContentLoaded', () => {
        loadTopApps();
        loadEvolution();
        loadComparison();
    });

    // 1. Top Apps
    async function loadTopApps() {
        try {
            const res = await fetch('/api/top-apps');
            const data = await res.json();
            let html = '<table><thead><tr><th>Rang</th><th>Application</th><th>Volume Total</th><th>Part Relative</th></tr></thead><tbody>';

            // On trouve le max pour faire des barres de progression relatives
            const max = Math.max(...data.map(d => parseFloat(d.total)));

            data.forEach((row, i) => {
                const pct = (row.total / max) * 100;
                html += `<tr>
                        <td><strong>#${i+1}</strong></td>
                        <td>${row.application}</td>
                        <td style="font-family:monospace; font-weight:700">${parseFloat(row.total).toFixed(2)}</td>
                        <td>
                            <div class="bar-container" style="width:150px"><div class="bar-fill" style="width:${pct}%; background-color: var(--primary)"></div></div>
                        </td>
                    </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('content-apps').innerHTML = html;
        } catch(e) { document.getElementById('content-apps').innerHTML = 'Erreur API'; }
    }

    // 2. Évolution
    async function loadEvolution() {
        try {
            const res = await fetch('/api/evolution');
            const data = await res.json();
            let html = '<table><thead><tr><th>Mois</th><th>Volume Cumulé</th><th>Tendance</th></tr></thead><tbody>';

            let prev = 0;
            data.forEach(row => {
                const current = parseFloat(row.total);
                let trend = current > prev ? '<span style="color:#10b981">↗ Hausse</span>' : '<span style="color:#ef4444">↘ Baisse</span>';
                if(prev === 0) trend = '-';

                html += `<tr>
                        <td>${row.mois_fmt}</td>
                        <td style="font-weight:700">${current.toFixed(2)}</td>
                        <td>${trend}</td>
                    </tr>`;
                prev = current;
            });
            html += '</tbody></table>';
            document.getElementById('content-evol').innerHTML = html;
        } catch(e) { document.getElementById('content-evol').innerHTML = 'Erreur API'; }
    }

    [cite_start]// 3. Comparaison (Utilitaires visuels demandés) [cite: 87]
    async function loadComparison() {
        try {
            const res = await fetch('/api/comparison');
            const data = await res.json();
            let html = '<table><thead><tr><th>Mois</th><th style="text-align:center">Stockage</th><th style="text-align:center">Réseau</th><th>Delta</th></tr></thead><tbody>';

            data.forEach(row => {
                const stock = parseFloat(row.stockage);
                const net = parseFloat(row.reseau);
                const diff = net - stock;

                // Calcul visuel de qui gagne
                const winnerClass = net > stock ? 'badge-res' : 'badge-stock';
                const winnerLabel = net > stock ? 'DOMINANTE RÉSEAU' : 'DOMINANTE STOCKAGE';
                const diffVal = Math.abs(diff).toFixed(2);

                html += `<tr>
                        <td style="font-weight:bold">${row.mois_fmt}</td>
                        <td>
                            <div class="stat-box" style="border-left: 4px solid var(--stockage)">
                                <span class="stat-val" style="color:var(--stockage)">${stock.toFixed(2)}</span>
                                <span class="stat-label">Stockage</span>
                            </div>
                        </td>
                        <td>
                            <div class="stat-box" style="border-left: 4px solid var(--reseau)">
                                <span class="stat-val" style="color:var(--reseau)">${net.toFixed(2)}</span>
                                <span class="stat-label">Réseau</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge ${winnerClass}">${winnerLabel}</span><br>
                            <small style="color:#64748b; margin-top:5px; display:block;">Écart: ${diffVal} unités</small>
                        </td>
                    </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('content-compare').innerHTML = html;
        } catch(e) { document.getElementById('content-compare').innerHTML = 'Erreur API'; }
    }
</script>
</body>
</html>
