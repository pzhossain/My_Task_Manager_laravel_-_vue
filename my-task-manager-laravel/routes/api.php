<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::post('/register', [AuthController::class, 'userRegistration']);
    Route::post('/login',[AuthController::class, 'userLogin']);

    Route::middleware('auth:sanctum')->group(function (){
        Route::post('/logout', [AuthController::class, 'userLogout']);
        Route::get('/me', [ProfileController::class, 'getProfile']);
        Route::patch('/update', [ProfileController::class, 'updateProfile']);        

        Route::get('/tasks/summary', [TaskController::class, 'summary']);
        Route::get('tasks/trashed', [TaskController::class, 'trashed']);   
        
        Route::apiResource('tasks', TaskController::class); 
       
        Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
        Route::delete('tasks/{id}/force', [TaskController::class, 'forceDelete']);
        Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
        Route::get('/tasksfilter', [TaskController::class, 'filter']);
        
    });
    
});
