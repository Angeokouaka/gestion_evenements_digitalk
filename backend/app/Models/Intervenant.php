<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervenant extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'email', 'specialite', 'bio'];

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class, 'evenement_intervenant');
    }
}
