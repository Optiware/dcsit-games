<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données du sujet (database/sql/data.sql) chargées dans les tables créées par les migrations.
 * La ligne « USE campus_it; » est retirée : la base utilisée est celle du .env.
 */
class DonneesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $chemin = database_path('sql/data.sql');
        if (! file_exists($chemin)) {
            $this->command->error('database/sql/data.sql introuvable.');

            return;
        }

        $sql = preg_replace('/^\s*USE\s+\w+\s*;\s*$/mi', '', file_get_contents($chemin));
        DB::unprepared($sql);

        $this->command->info(DB::table('consommation')->count().' consommations importées.');
    }
}
