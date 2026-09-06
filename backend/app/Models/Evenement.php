<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'date_debut', 'date_fin',
        'lieu', 'lien_visio', 'filiere', 'capacite_max', 'statut', 'categorie_id', 'organisateur_id',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function organisateur()
    {
        return $this->belongsTo(Organisateur::class);
    }

    public function intervenants()
    {
        return $this->belongsToMany(Intervenant::class, 'evenement_intervenant');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'inscriptions')
                     ->withPivot('statut', 'date_inscription')
                     ->withTimestamps();
    }
}