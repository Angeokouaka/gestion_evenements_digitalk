<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use App\Http\Resources\ParticipantResource;

class ParticipantController extends Controller
{
    public function index()
    {
        return ParticipantResource::collection(Participant::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'telephone' => 'nullable|string|max:9',
            'matricule' => 'nullable|string|max:9|unique:participants,matricule',
        ]);

        return (new ParticipantResource(Participant::create($validated)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Participant $participant)
    {
        return new ParticipantResource($participant);
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:participants,email,' . $participant->id,
            'telephone' => 'nullable|string|max:9',
            'matricule' => 'nullable|string|max:9|unique:participants,matricule,' . $participant->id,
        ]);

        $participant->update($validated);

        return new ParticipantResource($participant);
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();

        return response()->json(null, 204);
    }
}