<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'en_ligne'];

    protected $casts = [
        'en_ligne' => 'boolean',
    ];

    public function evenements()
    {
        return $this->hasMany(Evenement::class);
    }
}