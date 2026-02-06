<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CsvImportSeeder extends Seeder
{
    public function run()
    {
        // Chemins des fichiers SQL
        $schemaPath = database_path('sql/schema.sql');
        $dataPath = database_path('sql/data.sql');

        // 1. Exécution du schéma (Création des tables)
        if (file_exists($schemaPath)) {
            $this->command->info('Construction de la structure (Schema)...');
            // DB::unprepared permet d'exécuter du SQL brut contenant plusieurs requêtes
            DB::unprepared(file_get_contents($schemaPath));
        } else {
            $this->command->error('Fichier schema.sql introuvable dans database/sql/');
            return;
        }

        // 2. Insertion des données
        if (file_exists($dataPath)) {
            $this->command->info('Insertion des données (Data)...');
            DB::unprepared(file_get_contents($dataPath));
        } else {
            $this->command->error('Fichier data.sql introuvable dans database/sql/');
            return;
        }

        $this->command->info('✅ Base de données initialisée avec succès via SQL !');
    }
}
