<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_generation',
        'code_verification',
        'url_fichier',
        'date_envoi',
        'inscription_id',
    ];

    protected $casts = [
        'date_generation' => 'datetime',
        'date_envoi' => 'datetime',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }
}
