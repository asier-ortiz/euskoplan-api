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
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,10'); // 5 solicitudes cada 10 minutos
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:20,10');
    Route::get('find/email/{email}', [AuthController::class, 'findByEmail'])->middleware('throttle:20,1');
    Route::get('find/username/{username}', [AuthController::class, 'findByUsername'])->middleware('throttle:20,1');
});

// Reinicio de contraseña
Route::group(['prefix' => 'password'], function () {
    Route::post('sendEmail', [PasswordResetController::class, 'sendEmail'])->middleware('throttle:3,15');
    Route::get('find/{token}', [PasswordResetController::class, 'find'])->middleware('throttle:10,1');
    Route::post('reset', [PasswordResetController::class, 'reset'])->middleware('throttle:3,15');
});

// Verificación de cuenta
Route::group(['prefix' => 'account'], function () {
    Route::post('sendEmail', [EmailVerifyController::class, 'sendEmail'])->middleware('throttle:3,15');
    Route::get('find/{token}', [EmailVerifyController::class, 'find'])->middleware('throttle:10,1');
    Route::post('verify', [EmailVerifyController::class, 'verify'])->middleware('throttle:3,15');
});

// Alojamientos
Route::group(['prefix' => 'accommodation', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [AccommodationController::class, 'show']);
    Route::get('/results/filter', [AccommodationController::class, 'filter']);
    Route::get('/categories/{language}', [AccommodationController::class, 'categories']);
});

// Cuevas y restos arqueológicos
Route::group(['prefix' => 'cave', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [CaveController::class, 'show']);
    Route::get('/results/filter', [CaveController::class, 'filter']);
    Route::get('/categories/{language}', [CaveController::class, 'categories']);
});

// Recursos culturales
Route::group(['prefix' => 'cultural', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [CulturalController::class, 'show']);
    Route::get('/results/filter', [CulturalController::class, 'filter']);
    Route::get('/categories/{language}', [CulturalController::class, 'categories']);
});

// Eventos
Route::group(['prefix' => 'event', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [EventController::class, 'show']);
    Route::get('/results/filter', [EventController::class, 'filter']);
    Route::get('/categories/{language}', [EventController::class, 'categories']);
});

// Parques temáticos
Route::group(['prefix' => 'fair', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [FairController::class, 'show']);
    Route::get('/results/filter', [FairController::class, 'filter']);
});

// Localidades
Route::group(['prefix' => 'locality'], function () {
    Route::get('/result/{code}/{language}', [LocalityController::class, 'show'])->middleware('throttle:60,1');
    Route::get('/results/filter', [LocalityController::class, 'filter'])->middleware('throttle:30,1');
    Route::get('/results/search', [LocalityController::class, 'search'])->middleware('throttle:20,1');
    Route::get('/names', [LocalityController::class, 'names'])->middleware('throttle:60,1');
});

// Museos y centros de interpretación
Route::group(['prefix' => 'museum', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [MuseumController::class, 'show']);
    Route::get('/results/filter', [MuseumController::class, 'filter']);
    Route::get('/categories/{language}', [MuseumController::class, 'categories']);
});

// Espacios naturales
Route::group(['prefix' => 'natural', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [NaturalController::class, 'show']);
    Route::get('/results/filter', [NaturalController::class, 'filter']);
    Route::get('/categories/{language}', [NaturalController::class, 'categories']);
});

// Restaurantes
Route::group(['prefix' => 'restaurant', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/result/{code}/{language}', [RestaurantController::class, 'show']);
    Route::get('/results/filter', [RestaurantController::class, 'filter']);
    Route::get('/categories/{language}', [RestaurantController::class, 'categories']);
});

// Planes
Route::apiResource('plan', PlanController::class)->only(['index', 'show'])->middleware('throttle:30,1');
Route::get('plan/{id}/route/{profile}', [PlanController::class, 'route'])->middleware('throttle:20,1');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Usuario
    Route::group(['prefix' => 'user', 'middleware' => 'throttle:10,1'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::put('password', [AuthController::class, 'updatePassword']);
        Route::delete('destroy', [AuthController::class, 'destroy']);
    });

// Planes
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::apiResource('plan', PlanController::class)->except(['index', 'show'])->middleware('throttle:10,1');
        Route::get('plan/results/user', [PlanController::class, 'userPlans'])->middleware('throttle:10,1');
        Route::put('plan/upvote/{id}', [PlanController::class, 'upvote'])->middleware('throttle:30,1');
        Route::put('plan/downvote/{id}', [PlanController::class, 'downvote'])->middleware('throttle:30,1');
        Route::post('plan/suggest-itinerary', [PlanController::class, 'suggestItinerary'])->middleware('throttle:5,10');
    });

    // Pasos
    Route::group(['middleware' => 'throttle:20,1'], function () {
        Route::post('/step/{id}', [StepController::class, 'store']);
        Route::put('/step/{id}', [StepController::class, 'store']);
        Route::delete('/step/{id}', [StepController::class, 'store']);
    });

    // Favoritos
    Route::group(['prefix' => 'favourite', 'middleware' => 'throttle:20,1'], function () {
        Route::get('', [FavouriteController::class, 'show']);
        Route::post('', [FavouriteController::class, 'store']);
        Route::delete('/{id}', [FavouriteController::class, 'destroy']);
    });

});
