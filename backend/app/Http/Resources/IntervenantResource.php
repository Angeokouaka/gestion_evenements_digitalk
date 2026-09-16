<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class IntervenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'specialite' => $this->specialite,
            'bio' => $this->bio,
            'photo_url' => $this->photo ? Storage::disk('public')->url($this->photo) : null,
            'role' => $this->whenPivotLoaded('evenement_intervenant', function () {
                return $this->pivot->role;
            }),
        ];
    }
}
