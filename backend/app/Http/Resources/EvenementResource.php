<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvenementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'lieu' => $this->lieu,
            'lien_visio' => $this->lien_visio,
            'filiere' => $this->filiere,
            'capacite_max' => $this->capacite_max,
            'statut' => $this->statut,
            'categorie' => new CategorieResource($this->whenLoaded('categorie')),
            'organisateur' => new OrganisateurResource($this->whenLoaded('organisateur')),
            'intervenants' => IntervenantResource::collection($this->whenLoaded('intervenants')),
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
            'nb_inscrits' => $this->whenCounted('inscriptions'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}