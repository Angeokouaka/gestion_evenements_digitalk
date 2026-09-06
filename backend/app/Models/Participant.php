<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'matricule'];

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class, 'inscriptions')
                     ->withPivot('statut', 'date_inscription')
                     ->withTimestamps();
    }
}
