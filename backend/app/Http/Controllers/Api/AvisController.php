<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InscriptionResource;
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

    public function avisEvenement(int $evenementId)
    {
        return response()->json([
            'moyenne' => $this->service->moyenneEvenement($evenementId),
            'avis' => $this->service->avisEvenement($evenementId),
        ]);
    }

    public function avisIntervenant(int $intervenantId)
    {
        return response()->json([
            'moyenne' => $this->service->moyenneIntervenant($intervenantId),
            'avis' => $this->service->avisIntervenant($intervenantId),
        ]);
    }

    public function pageNotation(string $qrCode)
    {
        $inscription = $this->service->inscriptionParQrCode($qrCode);
        return new InscriptionResource($inscription);
    }

    public function noterEvenementParQrCode(Request $request, string $qrCode)
    {
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis = $this->service->noterEvenementParQrCode(
            $qrCode,
            $validated['note'],
            $validated['commentaire'] ?? null
        );

        return response()->json(['data' => $avis]);
    }

    public function noterIntervenantParQrCode(Request $request, string $qrCode, int $intervenantId)
    {
        $validated = $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $avis = $this->service->noterIntervenantParQrCode(
            $qrCode,
            $intervenantId,
            $validated['note'],
            $validated['commentaire'] ?? null
        );

        return response()->json(['data' => $avis]);
    }
}
