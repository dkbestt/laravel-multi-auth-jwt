<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::prefix('admin')->middleware('guest:admin')->group(
Route::prefix('admin')->group(
    // ['middleware' => 'auth:admin'],
    function () {
        Route::post('register', [AdminController::class, 'adminRegister']);
        Route::post('login', [AdminController::class, 'adminLogin']);

        Route::group(['middleware' => ['assign.guard:admin', 'jwt.verify']], function () {
            Route::post('logout', [AdminController::class, 'adminLogout']);
            Route::get('me', [AdminController::class, 'getAdmin']);
        });
    }
);

Route::prefix('user')->group(
    // ['middleware' => 'auth:user'],
    function () {
        Route::post('register', [UserController::class, 'userRegister']);
        Route::post('login', [UserController::class, 'userLogin']);

        // Route::group(['middleware' => ['assign.guard:user', 'jwt.auth']], function () {
        Route::group(['middleware' => ['assign.guard:user', 'jwt.verify']], function () {
            Route::post('logout', [UserController::class, 'userLogout']);
            Route::get('me', [UserController::class, 'getUser']);
        });
    }
);
