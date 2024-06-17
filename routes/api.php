<?php   

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

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

Route::post('/users-register', [UsersController::class, 'register']);

Route::post('/users-login', [UsersController::class, 'login']);

Route::put('/users-edit/{id}', [UsersController::class, 'edit'])->middleware('auth:sanctum');

Route::get('/users-view/{id}', [UsersController::class, 'view'])->middleware('auth:sanctum');

Route::delete('/users-delete/{id}', [UsersController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/users-forgot-password', [UsersController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
