<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'evenement_id',
        'date_inscription',
        'statut',
        'qr_code',
        'presence_arrivee',
        'date_presence_arrivee',
        'presence_depart',
        'date_presence_depart',
        'rappel_envoye',
        'date_rappel_envoye',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
        'presence_arrivee' => 'boolean',
        'date_presence_arrivee' => 'datetime',
        'presence_depart' => 'boolean',
        'date_presence_depart' => 'datetime',
        'rappel_envoye' => 'boolean',
        'date_rappel_envoye' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function certificat()
    {
        return $this->hasOne(Certificat::class);
    }
}
