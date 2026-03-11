<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DBController;
use App\Http\Controllers\ListViewController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/save-listview',[ListViewController::class,'save']);
Route::get('/get-listview',[ListViewController::class,'get']);

Route::get('/get-fields',[ListViewController::class,'getFields']);
Route::post('/save-fields',[ListViewController::class,'saveFields']);