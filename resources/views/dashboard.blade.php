<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campus IT | Tableau de Bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-base: #0b1120;
            --bg-panel: #111827;
            --bg-card: #1f2937;
            --cyan-glow: #06b6d4;
            --magenta: #ec4899;
            --yellow: #eab308;
            --green: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.05);
        }

        body { font-family: 'Inter', sans-serif; margin: 0; background-color: var(--bg-base); display: flex; height: 100vh; color: var(--text-main); overflow: hidden; }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-base); }
        ::-webkit-scrollbar-thumb { background: var(--bg-card); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--cyan-glow); }

        /* SIDEBAR */
        .sidebar { width: 280px; background-color: var(--bg-panel); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; }
        .brand { padding: 30px 20px; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 10px; color: var(--text-main); }

        .section-title { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; padding: 0 20px; margin-top: 20px; margin-bottom: 10px; }
        .menu { list-style: none; padding: 0; margin: 0; }
        .menu button {
            background: none; border: none; width: 100%; text-align: left; padding: 15px 20px;
            color: var(--text-muted); font-size: 0.95rem; cursor: pointer; transition: 0.3s; position: relative; font-family: 'Inter', sans-serif;
            border-left: 4px solid transparent;
        }
        .menu button:hover, .menu button.active { color: var(--text-main); background: rgba(6, 182, 212, 0.05); border-left-color: var(--cyan-glow); }

        /* MAIN CONTENT */
        .main-content { flex-grow: 1; overflow-y: auto; padding: 40px; padding-bottom: 80px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; }
        .header-top h2 { margin: 0; font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; }
        .header-top p { color: var(--text-muted); margin-top: 5px; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; }
        .period-badge { background: rgba(6, 182, 212, 0.1); border: 1px solid var(--cyan-glow); color: var(--cyan-glow); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }

        /* KPI CARDS */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .kpi-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 20px; position: relative; overflow: hidden; }
        .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 2px; }
        .kpi-card.cpu::before { background: var(--magenta); box-shadow: 0 0 10px var(--magenta); }
        .kpi-card.stockage::before { background: var(--yellow); box-shadow: 0 0 10px var(--yellow); }
        .kpi-card.reseau::before { background: var(--green); box-shadow: 0 0 10px var(--green); }
        .kpi-card.top-app::before { background: var(--cyan-glow); box-shadow: 0 0 10px var(--cyan-glow); }

        .kpi-data h4 { margin: 0; font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .kpi-data .val { font-size: 1.8rem; font-weight: 800; margin: 5px 0; font-family: 'JetBrains Mono', monospace; }
        .kpi-data .val span { font-size: 1rem; color: var(--text-muted); }
        .kpi-data .sub { font-size: 0.75rem; color: var(--text-muted); }
        .top-app .val { font-size: 1.4rem; color: var(--cyan-glow); }

        /* VIEWS (SPA) */
        .view-section { display: none; animation: fadeIn 0.4s ease-out forwards; }
        .view-section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* SUB-PANELS (Graphiques et Tableaux) */
        .panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 30px; margin-bottom: 20px; }
        .panel-header { margin-bottom: 25px; }
        .panel-subtitle { font-family: 'JetBrains Mono', monospace; color: var(--cyan-glow); font-size: 0.8rem; margin-bottom: 5px; }
        .panel-title { font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin: 0; }

        /* BENTO BOX GRID (NOUVEAU DESIGN DASHBOARD) */
        .bento-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .bento-large { grid-column: span 2; grid-row: span 2; }
        .bento-small { grid-column: span 1; }

        .summary-box { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid; transition: 0.2s; }
        .summary-box:hover { background: rgba(255,255,255,0.05); }

        /* CHART CONTAINER */
        .chart-container { position: relative; height: 300px; width: 100%; }

        /* TABLEAUX */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid var(--border); letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.02); font-size: 0.95rem; }
        tr:hover td { background-color: rgba(255,255,255,0.02); }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .td-highlight { color: var(--cyan-glow); font-weight: 700; }

        .bar-bg { background: rgba(255,255,255,0.05); height: 6px; border-radius: 3px; width: 100px; overflow: hidden; display: inline-block; vertical-align: middle; margin-left: 10px; }
        .bar-fill { height: 100%; background: var(--cyan-glow); box-shadow: 0 0 8px var(--cyan-glow); }

        /* BADGES */
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; font-family: 'JetBrains Mono', monospace; }
        .badge-alert { background: rgba(236, 72, 153, 0.1); color: var(--magenta); border: 1px solid var(--magenta); }
        .badge-ok { background: rgba(16, 185, 129, 0.1); color: var(--green); border: 1px solid var(--green); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">
        Campus IT
    </div>

    <div class="section-title">Analyses</div>
    <ul class="menu">
        <li><button class="active" onclick="switchTab('tab-dashboard', this)">Vue d'ensemble</button></li>
        <li><button onclick="switchTab('tab-apps', this)">Top Applications</button></li>
        <li><button onclick="switchTab('tab-evol', this)">Évolution Mensuelle</button></li>
        <li><button onclick="switchTab('tab-compare', this)">Comparaison Ressources</button></li>
        <li><button onclick="switchTab('tab-alertes', this)">Logs & Alertes</button></li>
    </ul>
</aside>

<main class="main-content">
    <div class="header-top">
        <div>
            <h2>Tableau de bord</h2>
            <p>Vue d'ensemble détaillée • Supervision IT</p>
        </div>
        <div class="period-badge">JAN - JUIN 2025</div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card cpu">
            <div class="kpi-data">
                <h4>CPU Total Alloué</h4>
                <div class="val mono" id="kpi-cpu">-- <span>vCPU</span></div>
                <div class="sub">Cumul 6 mois</div>
            </div>
        </div>
        <div class="kpi-card stockage">
            <div class="kpi-data">
                <h4>Volume Stockage</h4>
                <div class="val mono" id="kpi-stock">-- <span>Go</span></div>
                <div class="sub">Cumul 6 mois</div>
            </div>
        </div>
        <div class="kpi-card reseau">
            <div class="kpi-data">
                <h4>Trafic Réseau</h4>
                <div class="val mono" id="kpi-res">-- <span>Go</span></div>
                <div class="sub">Cumul 6 mois</div>
            </div>
        </div>
        <div class="kpi-card top-app">
            <div class="kpi-data">
                <h4>Application Principale</h4>
                <div class="val" id="kpi-topname">Chargement...</div>
                <div class="sub mono" id="kpi-topval">-- unités</div>
            </div>
        </div>
    </div>

    <div id="tab-dashboard" class="view-section active">
        <div class="bento-grid">

            <div class="panel bento-large">
                <div class="panel-header">
                    <div class="panel-subtitle">// TENDANCE GLOBALE</div>
                    <h3 class="panel-title">Évolution de la consommation totale</h3>
                </div>
                <div class="chart-container" style="height: 380px;">
                    <canvas id="mainDashboardChart"></canvas>
                </div>
            </div>

            <div class="panel bento-small">
                <div class="panel-header">
                    <div class="panel-subtitle">// RÉPARTITION</div>
                    <h3 class="panel-title">Volume par ressource</h3>
                </div>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>

            <div class="panel bento-small" style="display: flex; flex-direction: column; justify-content: flex-start;">
                <div class="panel-header" style="margin-bottom: 20px;">
                    <div class="panel-subtitle">// SYNTHÈSE RAPIDE</div>
                    <h3 class="panel-title">Points d'attention</h3>
                </div>

                <div class="summary-box" style="border-left-color: var(--yellow);">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 5px;">RESSOURCE DOMINANTE</div>
                    <strong id="synth-res" style="color: var(--text-main); font-size: 1.1rem; font-family: 'JetBrains Mono', monospace;">Calcul en cours...</strong>
                </div>

                <div class="summary-box" style="border-left-color: var(--cyan-glow);">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 5px;">APP LA PLUS GOURMANDE</div>
                    <strong id="synth-app" style="color: var(--text-main); font-size: 1.1rem; font-family: 'JetBrains Mono', monospace;">Calcul en cours...</strong>
                </div>

                <div class="summary-box" style="border-left-color: var(--magenta);">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 5px;">DERNIÈRE ALERTE (JUIN)</div>
                    <strong id="synth-alert" style="color: var(--magenta); font-size: 1rem; font-family: 'JetBrains Mono', monospace;">Calcul en cours...</strong>
                </div>
            </div>

        </div>
    </div>

    <div id="tab-apps" class="view-section">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-subtitle">// CLASSEMENT</div>
                <h3 class="panel-title">Les 5 applications les plus consommatrices</h3>
            </div>
            <div id="content-apps">Chargement...</div>
        </div>
    </div>

    <div id="tab-evol" class="view-section">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-subtitle">// DATA BRUTE</div>
                <h3 class="panel-title">Historique mensuel détaillé</h3>
            </div>
            <div id="content-evol">Chargement...</div>
        </div>
    </div>

    <div id="tab-compare" class="view-section">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-subtitle">// ANALYSE CROISÉE</div>
                <h3 class="panel-title">Stockage vs Réseau</h3>
            </div>
            <div class="chart-container" style="height: 350px; margin-bottom: 30px;">
                <canvas id="compareChart"></canvas>
            </div>
            <div id="content-compare"></div>
        </div>
    </div>

    <div id="tab-alertes" class="view-section">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-subtitle">// SÉCURITÉ & SURCHARGE</div>
                <h3 class="panel-title">Pics de charge détectés (Juin 2025)</h3>
            </div>
            <div id="content-alertes">Chargement...</div>
        </div>
    </div>

</main>

<script>
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'JetBrains Mono', monospace";
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

    let charts = {};

    function switchTab(tabId, btn) {
        document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        document.querySelectorAll('.menu button').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await Promise.all([
            fetchDataAndRender('/api/evolution', renderEvolution),
            fetchDataAndRender('/api/top-apps', renderTopApps),
            fetchDataAndRender('/api/repartition', renderRepartition),
            fetchDataAndRender('/api/comparison', renderComparison),
            fetchDataAndRender('/api/alertes', renderAlertes)
        ]);
    });

    async function fetchDataAndRender(url, renderFunction) {
        try {
            const response = await fetch(url);
            const data = await response.json();
            renderFunction(data);
        } catch (error) {
            console.error('Erreur sur ' + url, error);
        }
    }

    // 1. Évolution
    function renderEvolution(data) {
        const labels = data.map(d => d.mois_fmt);
        const values = data.map(d => parseFloat(d.total));

        const ctx = document.getElementById('mainDashboardChart').getContext('2d');
        let gradient = ctx.createLinearGradient(0, 0, 0, 380);
        gradient.addColorStop(0, 'rgba(6, 182, 212, 0.4)');
        gradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

        charts.main = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Volume Cumulé',
                    data: values,
                    borderColor: '#06b6d4',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#0b1120',
                    pointBorderColor: '#06b6d4',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top', labels: { color: '#94a3b8', font: {family: "'JetBrains Mono', monospace"} } },
                    tooltip: { callbacks: { label: function(context) { return context.parsed.y + ' unités'; } } }
                },
                scales: {
                    y: { beginAtZero: false, grid: { color: 'rgba(255,255,255,0.02)' }, ticks: { color: '#94a3b8', callback: function(value) { return value + ' u'; } } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        let html = '<table><thead><tr><th>Mois</th><th>Volume Cumulé</th><th>Tendance</th></tr></thead><tbody>';
        let prev = 0;
        data.forEach(row => {
            const current = parseFloat(row.total);
            let trend = current > prev ? '<span style="color:var(--green)">↗ Hausse</span>' : '<span style="color:var(--magenta)">↘ Baisse</span>';
            if(prev === 0) trend = '-';
            html += `<tr><td class="mono">${row.mois_fmt}</td><td class="mono td-highlight">${current.toFixed(2)}</td><td class="mono">${trend}</td></tr>`;
            prev = current;
        });
        html += '</tbody></table>';
        document.getElementById('content-evol').innerHTML = html;
    }

    // 2. Top Apps
    function renderTopApps(data) {
        if(data.length > 0) {
            document.getElementById('kpi-topname').innerText = data[0].application;
            document.getElementById('kpi-topval').innerText = parseFloat(data[0].total).toFixed(0) + ' unités';
            // MAJ du nouveau bloc Synthèse
            document.getElementById('synth-app').innerText = data[0].application;
        }

        let html = '<table><thead><tr><th>Rang</th><th>Application</th><th>Volume Total</th><th>Proportion</th></tr></thead><tbody>';
        const max = Math.max(...data.map(d => parseFloat(d.total)));
        data.forEach((row, i) => {
            const pct = (row.total / max) * 100;
            html += `<tr>
                <td class="mono">0${i+1}</td>
                <td style="font-weight:600; color:var(--text-main)">${row.application}</td>
                <td class="mono td-highlight">${parseFloat(row.total).toFixed(2)}</td>
                <td><div class="bar-bg"><div class="bar-fill" style="width:${pct}%"></div></div></td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('content-apps').innerHTML = html;
    }

    // 3. Comparaison
    function renderComparison(data) {
        const labels = data.map(d => d.mois_fmt);
        const stockData = data.map(d => parseFloat(d.stockage));
        const resData = data.map(d => parseFloat(d.reseau));

        const ctx = document.getElementById('compareChart').getContext('2d');
        charts.compare = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Stockage', data: stockData, backgroundColor: '#eab308', borderRadius: 4 },
                    { label: 'Réseau', data: resData, backgroundColor: '#10b981', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, labels: { color: '#94a3b8', font: {family: "'JetBrains Mono', monospace"} } },
                    tooltip: { callbacks: { label: function(context) { return context.dataset.label + ' : ' + context.parsed.y + ' Go'; } } }
                },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.02)' }, ticks: { color: '#94a3b8', callback: function(value) { return value + ' Go'; } } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        let html = '<table><thead><tr><th>Mois</th><th>Stockage</th><th>Réseau</th><th>Delta</th></tr></thead><tbody>';
        data.forEach(row => {
            const stock = parseFloat(row.stockage);
            const net = parseFloat(row.reseau);
            const diff = Math.abs(net - stock).toFixed(2);
            html += `<tr>
                <td class="mono">${row.mois_fmt}</td>
                <td class="mono" style="color:var(--yellow)">${stock.toFixed(2)} Go</td>
                <td class="mono" style="color:var(--green)">${net.toFixed(2)} Go</td>
                <td class="mono">± ${diff} Go</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('content-compare').innerHTML = html;
    }

    // 4. Répartition
    function renderRepartition(data) {
        data.forEach(row => {
            if(row.ressource.includes('CPU')) document.getElementById('kpi-cpu').innerHTML = `${parseFloat(row.total).toFixed(0)} <span>vCPU</span>`;
            if(row.ressource.includes('Stockage')) document.getElementById('kpi-stock').innerHTML = `${parseFloat(row.total).toFixed(0)} <span>Go</span>`;
            if(row.ressource.includes('Réseau')) document.getElementById('kpi-res').innerHTML = `${parseFloat(row.total).toFixed(0)} <span>Go</span>`;
        });

        // MAJ du nouveau bloc Synthèse (On prend la ressource n°1)
        if(data.length > 0) {
            document.getElementById('synth-res').innerText = data[0].ressource;
        }

        const ctx = document.getElementById('doughnutChart').getContext('2d');
        charts.doughnut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.ressource),
                datasets: [{
                    data: data.map(d => parseFloat(d.total)),
                    backgroundColor: ['#eab308', '#10b981', '#ec4899'],
                    borderColor: '#111827',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }, // Légende masquée pour gagner de la place dans la Bento box
                    tooltip: { callbacks: { label: function(context) { return ' ' + context.label + ' : ' + context.parsed + ' unités'; } } }
                },
                cutout: '70%'
            }
        });
    }

    // 5. Alertes
    function renderAlertes(data) {
        // MAJ du nouveau bloc Synthèse
        if(data.length > 0) {
            document.getElementById('synth-alert').innerText = data[0].application + ' (' + data[0].ressource + ')';
        }

        let html = '<table><thead><tr><th>Statut</th><th>Application</th><th>Ressource</th><th>Volume Pic</th></tr></thead><tbody>';
        data.forEach((row, i) => {
            const badge = i === 0 ? '<span class="badge badge-alert">CRITIQUE</span>' : '<span class="badge badge-ok">WARNING</span>';
            html += `<tr>
                <td>${badge}</td>
                <td style="color:var(--text-main)">${row.application}</td>
                <td class="mono">${row.ressource}</td>
                <td class="mono" style="color:${i===0 ? 'var(--magenta)' : 'var(--text-main)'}">${parseFloat(row.pic).toFixed(2)} u</td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('content-alertes').innerHTML = html;
    }
</script>
</body>
</html>
