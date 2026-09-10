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
            'qr_code' => $this->qr_code,
            'presence_arrivee' => $this->presence_arrivee,
            'date_presence_arrivee' => $this->date_presence_arrivee,
            'presence_depart' => $this->presence_depart,
            'date_presence_depart' => $this->date_presence_depart,
            'participant' => new ParticipantResource($this->whenLoaded('participant')),
            'evenement' => new EvenementResource($this->whenLoaded('evenement')),
        ];
    }
}
