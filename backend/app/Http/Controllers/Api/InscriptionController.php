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
        $inscriptions = $this->service->lister();
        return InscriptionResource::collection($inscriptions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'evenement_id' => 'required|exists:evenements,id|unique:inscriptions,evenement_id,NULL,id,participant_id,' . $request->participant_id,
            'statut' => 'nullable|in:en_attente,confirmee,annulee',
        ]);

        $inscription = $this->service->creer($validated);

        return (new InscriptionResource($inscription))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Inscription $inscription)
    {
        $inscription = $this->service->afficher($inscription);
        return new InscriptionResource($inscription);
    }

    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'statut' => 'sometimes|required|in:en_attente,confirmee,annulee',
        ]);

        $inscription = $this->service->modifier($inscription, $validated);

        return new InscriptionResource($inscription);
    }

    public function destroy(Inscription $inscription)
    {
        $this->service->supprimer($inscription);
        return response()->json(null, 204);
    }
}
