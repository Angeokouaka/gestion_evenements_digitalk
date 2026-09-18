<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisIntervenant extends Model
{
    use HasFactory;

    protected $fillable = ['note', 'commentaire', 'intervenant_id', 'evenement_id', 'participant_id'];

    public function intervenant()
    {
        return $this->belongsTo(Intervenant::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
