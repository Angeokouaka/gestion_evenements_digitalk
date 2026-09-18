<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AvisService;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function __construct(
        protected AvisService $service
    ) {}

    public function noterEvenement(Request $request, int $evenementId)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis = $this->service->noterEvenement(
            $evenementId,
            $validated['email'],
            $validated['note'],
            $validated['commentaire'] ?? null
        );

        return response()->json(['data' => $avis]);
    }

    public function noterIntervenant(Request $request, int $intervenantId)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'evenement_id' => 'required|exists:evenements,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis = $this->service->noterIntervenant(
            $intervenantId,
            $validated['evenement_id'],
            $validated['email'],
            $validated['note'],
            $validated['commentaire'] ?? null
        );

        return response()->json(['data' => $avis]);
    }
}
