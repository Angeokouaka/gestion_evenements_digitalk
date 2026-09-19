<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'date_debut', 'date_fin',
        'lieu', 'lien_visio', 'affiche', 'filiere', 'capacite_max', 'statut', 'categorie_id', 'organisateur_id',
        'qr_code', 'duree_fenetre_scan_debut', 'duree_fenetre_scan_fin',
        'rappel_actif', 'delai_rappel_heures',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'rappel_actif' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Evenement $evenement) {
            if (empty($evenement->qr_code)) {
                $evenement->qr_code = (string) Str::uuid();
            }
        });
    }

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
        return $this->belongsToMany(Intervenant::class, 'evenement_intervenant')
                     ->withPivot('role');
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

    public function avisEvenements()
    {
        return $this->hasMany(AvisEvenement::class);
    }
}
