<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InscriptionResource;
use App\Models\Inscription;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function index()
    {
        $inscriptions = Inscription::with(['participant', 'evenement'])->get();
        return InscriptionResource::collection($inscriptions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'evenement_id' => 'required|exists:evenements,id|unique:inscriptions,evenement_id,NULL,id,participant_id,' . $request->participant_id,
            'statut' => 'nullable|in:en_attente,confirmee,annulee',
        ]);

        $validated['statut'] = $validated['statut'] ?? 'en_attente';
        $validated['date_inscription'] = now();

        $inscription = Inscription::create($validated);
        $inscription->load(['participant', 'evenement']);

        return (new InscriptionResource($inscription))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Inscription $inscription)
    {
        $inscription->load(['participant', 'evenement']);
        return new InscriptionResource($inscription);
    }

    public function update(Request $request, Inscription $inscription)
    {
        $validated = $request->validate([
            'statut' => 'sometimes|required|in:en_attente,confirmee,annulee',
        ]);

        $inscription->update($validated);
        $inscription->load(['participant', 'evenement']);

        return new InscriptionResource($inscription);
    }

    public function destroy(Inscription $inscription)
    {
        $inscription->delete();
        return response()->json(null, 204);
    }
}