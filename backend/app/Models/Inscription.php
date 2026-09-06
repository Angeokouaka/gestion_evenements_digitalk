<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = ['participant_id', 'evenement_id', 'date_inscription', 'statut'];

    protected $casts = [
        'date_inscription' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }
}
