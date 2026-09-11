<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\OrganisateurController;
use App\Http\Controllers\Api\IntervenantController;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\EvenementController;
use App\Http\Controllers\Api\InscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentification
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Routes publiques (lecture seule)
Route::get('categories', [CategorieController::class, 'index']);
Route::get('categories/{categorie}', [CategorieController::class, 'show']);
Route::get('organisateurs', [OrganisateurController::class, 'index']);
Route::get('organisateurs/{organisateur}', [OrganisateurController::class, 'show']);
Route::get('intervenants', [IntervenantController::class, 'index']);
Route::get('intervenants/{intervenant}', [IntervenantController::class, 'show']);
Route::get('participants', [ParticipantController::class, 'index']);
Route::get('participants/{participant}', [ParticipantController::class, 'show']);
Route::get('evenements', [EvenementController::class, 'index']);
Route::get('evenements/{evenement}', [EvenementController::class, 'show']);

// Participants : creation libre (un participant s'enregistre sans compte)
Route::post('participants', [ParticipantController::class, 'store']);

// Inscriptions : libres (un participant s'inscrit sans compte)
// Inscriptions : libres (un participant s'inscrit sans compte)
Route::apiResource('inscriptions', InscriptionController::class);
Route::post('inscriptions/{inscription}/scanner-arrivee', [InscriptionController::class, 'scannerArrivee']);
Route::post('scan/{qrCode}', [InscriptionController::class, 'scannerParEmail']);

// Routes protegees (ecriture - necessite d'etre connecte en tant qu'Organisateur)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategorieController::class)
        ->parameters(['categories' => 'categorie'])
        ->except(['index', 'show']);

    Route::apiResource('organisateurs', OrganisateurController::class)
        ->only(['update', 'destroy']);

    Route::apiResource('intervenants', IntervenantController::class)
        ->except(['index', 'show']);

    Route::apiResource('participants', ParticipantController::class)
        ->except(['index', 'show', 'store']);

    Route::apiResource('evenements', EvenementController::class)
        ->except(['index', 'show']);

    Route::post('evenements/{evenement}/intervenants', [EvenementController::class, 'attachIntervenant']);
    Route::delete('evenements/{evenement}/intervenants/{intervenant}', [EvenementController::class, 'detachIntervenant']);
});
