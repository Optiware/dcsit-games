<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consommation extends Model
{
    protected $table = 'consommations';

    protected $primaryKey = 'conso_id';


    public $timestamps = false;


    protected $fillable = ['app_id', 'res_id', 'mois', 'volume'];

    public function application() {
        return $this->belongsTo(Application::class, 'app_id');
    }

    public function ressource() {
        return $this->belongsTo(Ressource::class, 'res_id');
    }
}
