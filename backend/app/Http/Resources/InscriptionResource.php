<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'statut' => $this->statut,
            'date_inscription' => $this->date_inscription,
            'participant' => new ParticipantResource($this->whenLoaded('participant')),
            'evenement' => new EvenementResource($this->whenLoaded('evenement')),
        ];
    }
}
