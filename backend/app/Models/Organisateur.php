<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Organisateur extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = ['nom', 'email', 'password', 'telephone', 'structure'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function evenements()
    {
        return $this->hasMany(Evenement::class);
    }
}
