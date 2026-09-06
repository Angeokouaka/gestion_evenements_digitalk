<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use App\Http\Resources\EvenementResource;
use Illuminate\Http\Request;
use App\Models\Intervenant;

class EvenementController extends Controller
{
    const FILIERES = ['ESITEC', 'IST', 'PGE', 'IMAP', 'MERCURE', 'ECONOMIE', 'BBA', 'LEA', 'SCHOOL OF LAW'];

    public function index(Request $request)
    {
        $query = Evenement::with(['categorie', 'organisateur']);

        if ($request->filled('filiere')) {
            $query->where('filiere', $request->filiere);
        }

        $evenements = $query->paginate(9);

        return EvenementResource::collection($evenements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'lieu' => 'nullable|string|max:255',
            'lien_visio' => 'nullable|url|max:255',
            'filiere' => 'nullable|in:' . implode(',', self::FILIERES),
            'capacite_max' => 'nullable|integer|min:1',
            'statut' => 'nullable|in:planifie,en_cours,termine,annule',
            'categorie_id' => 'required|exists:categories,id',
            'organisateur_id' => 'required|exists:organisateurs,id',
        ]);

        $validated['statut'] = $validated['statut'] ?? 'planifie';

        $evenement = Evenement::create($validated);
        $evenement->load(['categorie', 'organisateur']);

        return (new EvenementResource($evenement))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Evenement $evenement)
    {
        $evenement->load(['categorie', 'organisateur', 'intervenants', 'participants']);
        return new EvenementResource($evenement);
    }

    public function update(Request $request, Evenement $evenement)
    {
        if ($request->user()->id !== $evenement->organisateur_id) {
            return response()->json(['message' => 'Non autorise a modifier cet evenement.'], 403);
        }

        $validated = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after:date_debut',
            'lieu' => 'nullable|string|max:255',
            'lien_visio' => 'nullable|url|max:255',
            'filiere' => 'nullable|in:' . implode(',', self::FILIERES),
            'capacite_max' => 'nullable|integer|min:1',
            'statut' => 'nullable|in:planifie,en_cours,termine,annule',
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'organisateur_id' => 'sometimes|required|exists:organisateurs,id',
        ]);

        $evenement->update($validated);
        $evenement->load(['categorie', 'organisateur']);

        return new EvenementResource($evenement);
    }

    public function destroy(Request $request, Evenement $evenement)
    {
        if ($request->user()->id !== $evenement->organisateur_id) {
            return response()->json(['message' => 'Non autorise a supprimer cet evenement.'], 403);
        }

        $evenement->delete();
        return response()->json(null, 204);
    }

    public function attachIntervenant(Request $request, Evenement $evenement)
    {
        $validated = $request->validate([
            'intervenant_id' => 'required|exists:intervenants,id',
        ]);

        $evenement->intervenants()->syncWithoutDetaching($validated['intervenant_id']);

        return response()->json($evenement->load('intervenants'));
    }

    public function detachIntervenant(Evenement $evenement, Intervenant $intervenant)
    {
        $evenement->intervenants()->detach($intervenant->id);

        return response()->json($evenement->load('intervenants'));
    }
}