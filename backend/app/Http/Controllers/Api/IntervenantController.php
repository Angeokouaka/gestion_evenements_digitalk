<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IntervenantResource;
use App\Models\Intervenant;
use Illuminate\Http\Request;

class IntervenantController extends Controller
{
    public function index()
    {
        return IntervenantResource::collection(Intervenant::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'specialite' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        return (new IntervenantResource(Intervenant::create($validated)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Intervenant $intervenant)
    {
        return new IntervenantResource($intervenant);
    }

    public function update(Request $request, Intervenant $intervenant)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email',
            'specialite' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $intervenant->update($validated);

        return new IntervenantResource($intervenant);
    }

    public function destroy(Intervenant $intervenant)
    {
        $intervenant->delete();

        return response()->json(null, 204);
    }
}
