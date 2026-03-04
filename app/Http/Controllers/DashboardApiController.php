<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    // 1. OBLIGATOIRE : Top 5 Applications
    public function getTopApps(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('application', 'consommation.app_id', '=', 'application.app_id')
            ->selectRaw('application.nom as application, SUM(consommation.volume) as total')
            ->groupBy('application.nom')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        return response()->json($data);
    }

    // 2. OBLIGATOIRE : Évolution Mensuelle
    public function getEvolution(): JsonResponse
    {
        $data = DB::table('consommation')
            ->selectRaw('DATE_FORMAT(mois, "%Y-%m") as mois_fmt, SUM(volume) as total')
            ->groupBy('mois_fmt')
            ->orderBy('mois_fmt')
            ->get();
        return response()->json($data);
    }

    // 3. OBLIGATOIRE : Comparaison Stockage vs Réseau
    public function getComparison(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw('
                DATE_FORMAT(consommation.mois, "%Y-%m") as mois_fmt,
                SUM(CASE WHEN ressource.nom LIKE "%Stockage%" THEN consommation.volume ELSE 0 END) as stockage,
                SUM(CASE WHEN ressource.nom LIKE "%Réseau%" THEN consommation.volume ELSE 0 END) as reseau
            ')
            ->groupBy('mois_fmt')
            ->orderBy('mois_fmt')
            ->get();
        return response()->json($data);
    }

    // 4. BONUS 1 : Répartition des Ressources
    public function getRepartition(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw('ressource.nom as ressource, SUM(consommation.volume) as total')
            ->groupBy('ressource.nom')
            ->orderByDesc('total')
            ->get();
        return response()->json($data);
    }

    // 5. BONUS 2 : Alertes de Juin
    public function getAlertes(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('application', 'consommation.app_id', '=', 'application.app_id')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw('application.nom as application, ressource.nom as ressource, consommation.volume as pic')
            ->whereRaw('MONTH(consommation.mois) = 6')
            ->orderByDesc('pic')
            ->limit(5)
            ->get();
        return response()->json($data);
    }
}
