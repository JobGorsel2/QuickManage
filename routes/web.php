<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutAGOLController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 Route::get('/ping', fn() => 'pong');
;
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['verify' => true ]);

 
// Route::get('/oauth-callback.html', [App\Http\Controllers\DashboardController::class, 'index']);
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);                                       
                    
Route::get('/pages', [App\Http\Controllers\PageController::class, 'index']) ;                   
Route::get('/pages/create', [App\Http\Controllers\PageController::class, 'create']) ;                   
Route::post('/pages/store', [App\Http\Controllers\PageController::class, 'store']) ;                    
Route::get('/pages/view/{unique}', [App\Http\Controllers\PageController::class, 'show']) ;                  
Route::get('/pages/edit/{unique}', [App\Http\Controllers\PageController::class, 'edit']) ;                  
Route::patch('/pages/update/{id}', [App\Http\Controllers\PageController::class, 'update']) ;                    
Route::delete('/pages/delete/{id}', [App\Http\Controllers\PageController::class, 'destroy']) ;                  
                    
Route::get('/folders', [App\Http\Controllers\FoldersController::class, 'index']) ;
Route::get('/folder/create', [App\Http\Controllers\FoldersController::class, 'create']) ;
Route::post('/folders/store', [App\Http\Controllers\FoldersController::class, 'store']) ;
Route::get('/folders/view/{id}', [App\Http\Controllers\FoldersController::class, 'show']) ;
Route::delete('/folders/delete/{id}', [App\Http\Controllers\FoldersController::class, 'delete']) ;

Route::get('/profile/{id}/edit', [App\Http\Controllers\ProfileController::class, 'edit']);
Route::patch('/profile/{id}', [App\Http\Controllers\ProfileController::class, 'update']);

Route::get('/templates', [App\Http\Controllers\TemplatesController::class, 'index']);
Route::get('/template/create', [App\Http\Controllers\TemplatesController::class, 'create']);
Route::get('/template/edit/{unique}', [App\Http\Controllers\TemplatesController::class, 'edit']);
Route::patch('/template/update/{unique}', [App\Http\Controllers\TemplatesController::class, 'update']);
Route::post('/template/store', [App\Http\Controllers\TemplatesController::class, 'store']);

Route::get('/account', [App\Http\Controllers\AccountController::class, 'index']);

Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index']);

Auth::routes();
 
                 
Route::get('/arcgis/loginAGOL', function () {
    $portal = rtrim(config('services.arcgis.portal'), '/');

    return redirect(
        $portal . '/sharing/oauth2/authorize?' . http_build_query([
            'client_id'     => config('services.arcgis.client_id'),
            'response_type' => 'code',
            'redirect_uri'  => config('services.arcgis.redirect_uri'),
        ])
    ); 
})->name('arcgis.loginAGOL');

Route::middleware(['arcgis.required'])->group(function () {
 Route::get('/testAI', [App\Http\Controllers\AIController::class, 'index'])->name('testAI');
 Route::post('/logoutAGOL', [LogoutAGOLController::class, 'logout'])
    ->name('logoutAGOL');
});
                                      
Route::get('/oauth-callback', [App\Http\Controllers\AIController::class, 'callback']);  

// ai routes
Route::post('/ai/nl2where', [App\Http\Controllers\AIController::class, 'nl2where'])
    ->name('ai.nl2where');

Route::get('/ai/nl2where-stream', [App\Http\Controllers\AIController::class, 'nl2whereStream']);