<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisateur;
use Illuminate\Http\Request;
use App\Http\Resources\OrganisateurResource;

class OrganisateurController extends Controller
{
    public function index()
{
    return OrganisateurResource::collection(Organisateur::all());
}

public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'email' => 'required|email|unique:organisateurs,email',
        'telephone' => 'nullable|string|max:20',
        'structure' => 'nullable|string|max:255',
    ]);

    return (new OrganisateurResource(Organisateur::create($validated)))
        ->response()
        ->setStatusCode(201);
}

public function show(Organisateur $organisateur)
{
    return new OrganisateurResource($organisateur);
}

public function update(Request $request, Organisateur $organisateur)
{
    if ($request->user()->id !== $organisateur->id) {
        return response()->json(['message' => 'Non autorisé à modifier ce profil.'], 403);
    }

    $validated = $request->validate([
        'nom' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email|unique:organisateurs,email,' . $organisateur->id,
        'telephone' => 'nullable|string|max:20',
        'structure' => 'nullable|string|max:255',
    ]);

    $organisateur->update($validated);

    return new OrganisateurResource($organisateur);
}

public function destroy(Request $request, Organisateur $organisateur)
{
    if ($request->user()->id !== $organisateur->id) {
        return response()->json(['message' => 'Non autorisé à supprimer ce profil.'], 403);
    }

    $organisateur->delete();

    return response()->json(null, 204);
}
}
