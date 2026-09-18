<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisEvenement extends Model
{
    use HasFactory;

    protected $fillable = ['note', 'commentaire', 'evenement_id', 'participant_id'];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
