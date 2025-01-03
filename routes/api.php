<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {

    // Rutas públicas
    Route::post('auth/login', [AuthController::class, 'login']);

    // Rutas protegidas con JWT
    Route::group(['middleware' => 'jwt.auth'], function () {

        // Rutas de inscripción
        Route::post('inscripcionpublica', [InscriptionController::class, 'registroInscripcionPublic']);
        Route::get('enviarcorreo', [InscriptionController::class, 'enviarCorreo']);
        Route::get('getchairsopen', [ChairController::class, 'getChairsOpen']);

        // Rutas de autenticación
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('me', [AuthController::class, 'me']);
        });

        // Rutas de recursos protegidos
        Route::apiResources([
            'students'     => StudentController::class,
            'facilitators' => FacilitatorController::class,
            'chairs'       => ChairController::class,
            'modules'      => ModuleController::class,
            'levels'       => LevelController::class,
            'users'        => UserController::class,
            'roles'        => RoleController::class,
        ]);

        // Rutas adicionales de inscripción
        Route::group(['prefix' => 'inscriptions'], function () {
            Route::get('/', [InscriptionController::class, 'getInscriptionPublic']);
            Route::get('updatestatus/{id}', [InscriptionController::class, 'changeStatus']);
        });
    });
});