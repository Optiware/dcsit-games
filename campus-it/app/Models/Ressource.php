<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ressource extends Model
{
    protected $table = 'ressources';

    // IMPORTANT : Clé primaire "res_id"
    protected $primaryKey = 'res_id';

    public $timestamps = false;

    // On autorise l'insertion de ces colonnes
    protected $fillable = ['nom', 'unite'];
}
