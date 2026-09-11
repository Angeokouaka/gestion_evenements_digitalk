<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InscriptionResource;
use App\Models\Inscription;
use App\Services\InscriptionService;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function __construct(
        protected InscriptionService $service
    ) {}

    public function index()
    {
        return InscriptionResource::collection($this->service->lister());
    }

        public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'evenement_id' => 'required|exists:evenements,id|unique:inscriptions,evenement_id,NULL,id,participant_id,' . $request->participant_id,
            'statut' => 'nullable|in:en_attente,confirmee,annulee',
        ]);

        $resultat = $this->service->creer($validated);

        if (is_array($resultat) && ($resultat['liste_attente'] ?? false)) {
            return response()->json([
                'liste_attente' => true,
                'position' => $resultat['position'],
                'message' => "L'evenement est complet. Vous avez ete ajoute a la liste d'attente en position {$resultat['position']}.",
            ], 200);
        }

        return (new InscriptionResource($resultat))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Inscription $inscription)
    {
        return new InscriptionResource($this->service->afficher($inscription));
    }

    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'statut' => 'sometimes|required|in:en_attente,confirmee,annulee',
        ]);

        return new InscriptionResource($this->service->modifier($inscription, $validated));
    }

    public function destroy(Inscription $inscription)
    {
        $this->service->supprimer($inscription);
        return response()->json(null, 204);
    }

    public function scannerArrivee(Inscription $inscription)
    {
        $inscription = $this->service->confirmerPresenceArrivee($inscription);
        return new InscriptionResource($inscription);
    }

    
}
