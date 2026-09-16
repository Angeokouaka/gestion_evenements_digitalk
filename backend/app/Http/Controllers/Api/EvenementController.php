<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evenement;
use App\Http\Resources\EvenementResource;
use App\Services\EvenementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Intervenant;

class EvenementController extends Controller
{
    const FILIERES = ['ESITEC', 'IST', 'PGE', 'IMAP', 'MERCURE', 'ECONOMIE', 'BBA', 'LEA', 'SCHOOL OF LAW'];
    const ROLES_INTERVENTION = ['Intervenant', 'Moderateur', 'Animateur'];

    public function __construct(
        protected EvenementService $service
    ) {}

    public function index(Request $request)
    {
        $evenements = $this->service->lister($request->filled('filiere') ? $request->filiere : null);

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
            'affiche' => 'nullable|image|max:5120',
            'filiere' => 'nullable|in:' . implode(',', self::FILIERES),
            'capacite_max' => 'nullable|integer|min:1',
            'statut' => 'nullable|in:planifie,en_cours,termine,annule',
            'categorie_id' => 'required|exists:categories,id',
            'organisateur_id' => 'required|exists:organisateurs,id',
        ]);

        if ($request->hasFile('affiche')) {
            $validated['affiche'] = $request->file('affiche')->store('affiches', 'public');
        }

        $evenement = $this->service->creer($validated);

        return (new EvenementResource($evenement))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Evenement $evenement)
    {
        $evenement = $this->service->afficher($evenement);
        return new EvenementResource($evenement);
    }

    public function update(Request $request, Evenement $evenement)
    {
        $validated = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after:date_debut',
            'lieu' => 'nullable|string|max:255',
            'lien_visio' => 'nullable|url|max:255',
            'affiche' => 'nullable|image|max:5120',
            'filiere' => 'nullable|in:' . implode(',', self::FILIERES),
            'capacite_max' => 'nullable|integer|min:1',
            'statut' => 'nullable|in:planifie,en_cours,termine,annule',
            'categorie_id' => 'sometimes|required|exists:categories,id',
            'organisateur_id' => 'sometimes|required|exists:organisateurs,id',
        ]);

        if ($request->hasFile('affiche')) {
            if ($evenement->affiche) {
                Storage::disk('public')->delete($evenement->affiche);
            }
            $validated['affiche'] = $request->file('affiche')->store('affiches', 'public');
        }

        $evenement = $this->service->modifier($request->user()->id, $evenement, $validated);

        return new EvenementResource($evenement);
    }

    public function destroy(Request $request, Evenement $evenement)
    {
        $this->service->supprimer($request->user()->id, $evenement);
        return response()->json(null, 204);
    }

    public function attachIntervenant(Request $request, Evenement $evenement)
    {
        $validated = $request->validate([
            'intervenant_id' => 'required|exists:intervenants,id',
            'role' => 'nullable|in:' . implode(',', self::ROLES_INTERVENTION),
        ]);

        $evenement = $this->service->ajouterIntervenant(
            $evenement,
            $validated['intervenant_id'],
            $validated['role'] ?? 'Intervenant'
        );

        return response()->json($evenement);
    }

    public function detachIntervenant(Evenement $evenement, Intervenant $intervenant)
    {
        $evenement = $this->service->retirerIntervenant($evenement, $intervenant->id);

        return response()->json($evenement);
    }
}
