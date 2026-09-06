<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisateurResource;
use App\Models\Organisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:organisateurs,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'structure' => 'nullable|string|max:255',
        ]);

        $organisateur = Organisateur::create($validated);

        $token = $organisateur->createToken('digitalk-token')->plainTextToken;

        return response()->json([
            'organisateur' => new OrganisateurResource($organisateur),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $organisateur = Organisateur::where('email', $validated['email'])->first();

        if (! $organisateur || ! Hash::check($validated['password'], $organisateur->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants incorrects.'],
            ]);
        }

        $token = $organisateur->createToken('digitalk-token')->plainTextToken;

        return response()->json([
            'organisateur' => new OrganisateurResource($organisateur),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }
}
