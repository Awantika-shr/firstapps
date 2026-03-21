 <?php
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\DBController;
// use App\Http\Controllers\ListViewController;

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::post('/save-listview',[ListViewController::class,'save']);
// Route::get('/get-listview',[ListViewController::class,'get']);

// Route::get('/get-fields',[ListViewController::class,'getFields']);
// Route::post('/save-fields',[ListViewController::class,'saveFields']); 



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListViewController;

// API routes for saving/loading data
Route::post('/save-listview', [ListViewController::class, 'save']);   
Route::get('/get-listview', [ListViewController::class, 'get']);

Route::get('/get-fields', [ListViewController::class, 'getFields']);
Route::post('/save-fields', [ListViewController::class, 'saveFields']); 

Route::post('/save-editview',[ListViewController::class,'saveEditView']);
Route::get('/get-editview',[ListViewController::class,'getEditView']);

// Catch-all route for Vue Router (must be at the bottom)
Route::get('/{any}', function () {
    return view('welcome'); // Vue app mounts here
})->where('any', '.*');