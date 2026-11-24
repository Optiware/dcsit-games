<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Données des graphiques du tableau de bord.
 * Le mois est lu avec SUBSTR (« 2025-06-01 » → « 2025-06 ») : la même requête marche
 * avec MySQL / MariaDB en production et SQLite pendant les tests.
 */
class DashboardApiController extends Controller
{
    private const MOIS = 'SUBSTR(consommation.mois, 1, 7)';

    // 1. Top 5 des applications les plus consommatrices
    public function getTopApps(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('application', 'consommation.app_id', '=', 'application.app_id')
            ->selectRaw('application.nom as application, ROUND(SUM(consommation.volume), 2) as total')
            ->groupBy('application.nom')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json($data);
    }

    // 2. Évolution mensuelle de la consommation totale
    public function getEvolution(): JsonResponse
    {
        $data = DB::table('consommation')
            ->selectRaw(self::MOIS.' as mois_fmt, ROUND(SUM(consommation.volume), 2) as total')
            ->groupByRaw(self::MOIS)
            ->orderBy('mois_fmt')
            ->get();

        return response()->json($data);
    }

    // 3. Stockage et réseau, mois par mois
    public function getComparison(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw(
                self::MOIS.' as mois_fmt,
                ROUND(SUM(CASE WHEN ressource.nom = ? THEN consommation.volume ELSE 0 END), 2) as stockage,
                ROUND(SUM(CASE WHEN ressource.nom = ? THEN consommation.volume ELSE 0 END), 2) as reseau',
                ['Stockage', 'Réseau'],
            )
            ->groupByRaw(self::MOIS)
            ->orderBy('mois_fmt')
            ->get();

        return response()->json($data);
    }

    // 4. Bonus : répartition par type de ressource
    public function getRepartition(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw('ressource.nom as ressource, ROUND(SUM(consommation.volume), 2) as total')
            ->groupBy('ressource.nom')
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    // 5. Bonus : les 5 plus gros pics de juin
    public function getAlertes(): JsonResponse
    {
        $data = DB::table('consommation')
            ->join('application', 'consommation.app_id', '=', 'application.app_id')
            ->join('ressource', 'consommation.res_id', '=', 'ressource.res_id')
            ->selectRaw('application.nom as application, ressource.nom as ressource, consommation.volume as pic')
            ->whereRaw('SUBSTR(consommation.mois, 6, 2) = ?', ['06'])
            ->orderByDesc('pic')
            ->limit(5)
            ->get();

        return response()->json($data);
    }
}
