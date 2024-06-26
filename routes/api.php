<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiController;


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

Route::get('about-us', [ApiController::class, 'aboutus']);
// Route::get('get-data/{name}', [ApiController::class, 'get_data']);
Route::post('add-data', [ApiController::class, 'add_data']);

Route::post('generate-pdf', [ApiController::class, 'generateAndSavePDF']);
