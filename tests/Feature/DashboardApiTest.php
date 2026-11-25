<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('application')->insert([
            ['app_id' => 1, 'nom' => 'Intranet'],
            ['app_id' => 2, 'nom' => 'Helpdesk'],
        ]);
        DB::table('ressource')->insert([
            ['res_id' => 1, 'nom' => 'Stockage', 'unite' => 'Go'],
            ['res_id' => 2, 'nom' => 'Réseau', 'unite' => 'Go'],
        ]);
        DB::table('consommation')->insert([
            ['app_id' => 1, 'res_id' => 1, 'mois' => '2025-05-01', 'volume' => 100],
            ['app_id' => 1, 'res_id' => 2, 'mois' => '2025-06-01', 'volume' => 40],
            ['app_id' => 2, 'res_id' => 1, 'mois' => '2025-06-01', 'volume' => 250],
        ]);
    }

    public function test_le_tableau_de_bord_s_affiche(): void
    {
        $this->get('/')->assertOk()->assertSee('/api/top-apps');
    }

    public function test_le_tableau_de_bord_propose_le_francais_et_l_anglais(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-lang="fr"', false)
            ->assertSee('data-lang="en"', false)
            ->assertSee('Monthly Trend');
    }

    public function test_top_des_applications(): void
    {
        $donnees = $this->getJson('/api/top-apps')->assertOk()->json();

        $this->assertSame(['Helpdesk', 'Intranet'], array_column($donnees, 'application'));
        $this->assertEquals([250, 140], array_map('floatval', array_column($donnees, 'total')));
    }

    public function test_evolution_par_mois(): void
    {
        $donnees = $this->getJson('/api/evolution')->assertOk()->json();

        $this->assertSame(['2025-05', '2025-06'], array_column($donnees, 'mois_fmt'));
        $this->assertEquals([100, 290], array_map('floatval', array_column($donnees, 'total')));
    }

    public function test_comparaison_stockage_et_reseau(): void
    {
        $juin = $this->getJson('/api/comparison')->assertOk()->json()[1];

        $this->assertSame('2025-06', $juin['mois_fmt']);
        $this->assertEquals(250, (float) $juin['stockage']);
        $this->assertEquals(40, (float) $juin['reseau']);
    }

    public function test_repartition_par_ressource(): void
    {
        $donnees = $this->getJson('/api/repartition')->assertOk()->json();

        $this->assertSame(['Stockage', 'Réseau'], array_column($donnees, 'ressource'));
    }

    public function test_alertes_de_juin(): void
    {
        $donnees = $this->getJson('/api/alertes')->assertOk()->json();

        $this->assertCount(2, $donnees);
        $this->assertSame('Helpdesk', $donnees[0]['application']);
        $this->assertEquals(250, (float) $donnees[0]['pic']);
    }

    public function test_les_donnees_du_sujet_se_chargent(): void
    {
        DB::table('consommation')->delete();
        DB::table('application')->delete();
        DB::table('ressource')->delete();

        $this->seed();

        $this->assertSame(5, DB::table('application')->count());
        $this->assertGreaterThan(0, DB::table('consommation')->count());
    }
}
