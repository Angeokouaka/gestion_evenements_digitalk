<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Resources\CategorieResource;

class CategorieController extends Controller
{
    public function index()
{
    return CategorieResource::collection(Categorie::all());
}
    public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    return new CategorieResource(Categorie::create($validated));
}
public function show(Categorie $categorie)
{
    return new CategorieResource($categorie->load('evenements'));
}

    public function update(Request $request, Categorie $categorie)
{
    $validated = $request->validate([
        'nom' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $categorie->update($validated);

    return new CategorieResource($categorie);
}

    public function destroy(Categorie $categorie)
    {
        $categorie->delete();

        return response()->json(null, 204);
    }
}
