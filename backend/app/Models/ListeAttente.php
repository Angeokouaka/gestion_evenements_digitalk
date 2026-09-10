<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeAttente extends Model
{
    use HasFactory;

    protected $table = 'liste_attentes';

    protected $fillable = [
        'date_ajout',
        'position',
        'evenement_id',
        'participant_id',
    ];

    protected $casts = [
        'date_ajout' => 'datetime',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
