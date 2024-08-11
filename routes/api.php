<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaveController;
use App\Http\Controllers\CulturalController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FairController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\MuseumController;
use App\Http\Controllers\NaturalController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

// Registro y login
Route::group(['prefix' => 'user'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('find/email/{email}', [AuthController::class, 'findByEmail']);
    Route::get('find/username/{username}', [AuthController::class, 'findByUsername']);
});

// Reinicio de contraseña
Route::group(['prefix' => 'password'], function () {
    Route::post('sendEmail', [PasswordResetController::class, 'sendEmail']);
    Route::get('find/{token}', [PasswordResetController::class, 'find']);
    Route::post('reset', [PasswordResetController::class, 'reset']);
});

// Verificación de cuenta
Route::group(['prefix' => 'account'], function () {
    Route::post('sendEmail', [EmailVerifyController::class, 'sendEmail']);
    Route::get('find/{token}', [EmailVerifyController::class, 'find']);
    Route::post('verify', [EmailVerifyController::class, 'verify']);
});

// Alojamientos
Route::group(['prefix' => 'accommodation'], function () {
    Route::get('/result/{code}/{language}', [AccommodationController::class, 'show']);
    Route::get('/results/filter', [AccommodationController::class, 'filter']);
    Route::get('/categories/{language}', [AccommodationController::class, 'categories']);
});

// Cuevas y restos arqueológicos
Route::group(['prefix' => 'cave'], function () {
    Route::get('/result/{code}/{language}', [CaveController::class, 'show']);
    Route::get('/results/filter', [CaveController::class, 'filter']);
    Route::get('/categories/{language}', [CaveController::class, 'categories']);
});

// Recursos culturales
Route::group(['prefix' => 'cultural'], function () {
    Route::get('/result/{code}/{language}', [CulturalController::class, 'show']);
    Route::get('/results/filter', [CulturalController::class, 'filter']);
    Route::get('/categories/{language}', [CulturalController::class, 'categories']);
});

// Eventos
Route::group(['prefix' => 'event'], function () {
    Route::get('/result/{code}/{language}', [EventController::class, 'show']);
    Route::get('/results/filter', [EventController::class, 'filter']);
    Route::get('/categories/{language}', [EventController::class, 'categories']);
});

// Parques temáticos
Route::group(['prefix' => 'fair'], function () {
    Route::get('/result/{code}/{language}', [FairController::class, 'show']);
    Route::get('/results/filter', [FairController::class, 'filter']);
});

// Localidades
Route::group(['prefix' => 'locality'], function () {
    Route::get('/result/{code}/{language}', [LocalityController::class, 'show']);
    Route::get('/results/filter', [LocalityController::class, 'filter']);
    Route::get('/results/search', [LocalityController::class, 'search']);
    Route::get('/names', [LocalityController::class, 'names']);
});

// Museos y centros de interpretación
Route::group(['prefix' => 'museum'], function () {
    Route::get('/result/{code}/{language}', [MuseumController::class, 'show']);
    Route::get('/results/filter', [MuseumController::class, 'filter']);
    Route::get('/categories/{language}', [MuseumController::class, 'categories']);
});

// Espacios naturales
Route::group(['prefix' => 'natural'], function () {
    Route::get('/result/{code}/{language}', [NaturalController::class, 'show']);
    Route::get('/results/filter', [NaturalController::class, 'filter']);
    Route::get('/categories/{language}', [NaturalController::class, 'categories']);
});

// Restaurantes
Route::group(['prefix' => 'restaurant'], function () {
    Route::get('/result/{code}/{language}', [RestaurantController::class, 'show']);
    Route::get('/results/filter', [RestaurantController::class, 'filter']);
    Route::get('/categories/{language}', [RestaurantController::class, 'categories']);
});

// Planes
Route::apiResource('plan', PlanController::class)->only(['index', 'show']);
Route::get('plan/{id}/route/{profile}', [PlanController::class, 'route']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Usuario
    Route::group(['prefix' => 'user'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::put('password', [AuthController::class, 'updatePassword']);
        Route::delete('destroy', [AuthController::class, 'destroy']);
    });

    // Planes
    Route::apiResource('plan', PlanController::class)->except(['index', 'show']);
    Route::get('plan/results/user', [PlanController::class, 'userPlans']);
    Route::put('plan/upvote/{id}', [PlanController::class, 'upvote']);
    Route::put('plan/downvote/{id}', [PlanController::class, 'downvote']);

    // Pasos
    Route::apiResource('step', StepController::class)->only(['store', 'update', 'destroy']);

    // Favoritos
    Route::group(['prefix' => 'favourite'], function () {
        Route::get('', [FavouriteController::class, 'show']);
        Route::post('', [FavouriteController::class, 'store']);
        Route::delete('/{id}', [FavouriteController::class, 'destroy']);
    });

});
