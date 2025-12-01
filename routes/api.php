<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register',      [AuthController::class, 'apiRegister']);     
Route::post('/token/login',   [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',                 [AuthController::class, 'apiMe']);
    Route::post('/token/logout',      [AuthController::class, 'apiLogout']);
    Route::post('/tokens/logout-all', [AuthController::class, 'apiLogoutAll']);
});
