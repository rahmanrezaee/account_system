<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $user = Auth::user();
    $success['token'] = $user->createToken('appToken')->accessToken;
    return $request->user();
});

Route::get("FactorList",function (Request $request){

    return Response::json(\App\Models\Sale_Factor::paginate(3));
});

Route::post("login",function (Request $request){

    return Response::json(Auth::attempt($request->input()));
});
