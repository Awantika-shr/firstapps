<?php

use App\Http\Controllers\DragController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/user',function (Request $request){
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/students',[StudentController::class,'get']);
Route::post('/students',[StudentController::class,'add']);
Route::put('/students/{student}',[StudentController::class,'update']);
Route::delete('/students/{student}',[StudentController::class,'destroy']);

