<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    // Nom de table du sujet (au singulier)
    protected $table = 'application';

    // IMPORTANT : On définit la clé primaire car ce n'est pas "id" mais "app_id"
    protected $primaryKey = 'app_id';

    // On désactive les timestamps (created_at/updated_at) car absents de tes CSV
    public $timestamps = false;

    // On autorise l'insertion de cette colonne
    protected $fillable = ['nom'];
}
