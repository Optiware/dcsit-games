<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    // Onglet 1: Top 5
    public function getTopApps() {
        // On récupère le Top 5 directement en SQL
        $results = DB::select("
            SELECT a.nom as application, SUM(c.volume) as total
            FROM consommation c
            JOIN application a ON a.app_id = c.app_id
            GROUP BY a.nom
            ORDER BY total DESC
            LIMIT 5
        ");
        return response()->json($results);
    }

    // Onglet 2: Évolution
    public function getEvolution() {
        $results = DB::select("
            SELECT DATE_FORMAT(mois, '%Y-%m') as mois_fmt, SUM(volume) as total
            FROM consommation
            GROUP BY mois_fmt
            ORDER BY mois_fmt ASC
        ");
        return response()->json($results);
    }

    // Onglet 3: Comparaison Stockage vs Réseau
    public function getComparison() {
        // Requête pivot pour avoir Stockage et Réseau sur la même ligne par mois
        $results = DB::select("
            SELECT
                DATE_FORMAT(c.mois, '%Y-%m') as mois_fmt,
                SUM(CASE WHEN r.nom = 'Stockage' THEN c.volume ELSE 0 END) as stockage,
                SUM(CASE WHEN r.nom = 'Réseau' THEN c.volume ELSE 0 END) as reseau
            FROM consommation c
            JOIN ressource r ON r.res_id = c.res_id
            GROUP BY mois_fmt
            ORDER BY mois_fmt ASC
        ");
        return response()->json($results);
    }
}
