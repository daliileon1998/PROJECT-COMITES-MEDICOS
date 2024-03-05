<?php

use App\Http\Controllers\ComiteController;
use App\Http\Controllers\CompromisoController;
use App\Http\Controllers\GestionComiteController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TipoUsuarioController;
use App\Http\Controllers\usuarioController;
use App\Models\Compromiso;
use App\Models\TipoUsuario;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CompromisoRepository;

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

Route::get('/', function () {
    return redirect()->route('login');
    /*return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);*/
});

Route::get('/dashboard', function (CompromisoRepository $compromisoRepository) {
    if (Auth::check()) {
        $user = Auth::user();
        $compromisos = $compromisoRepository->getCompromisosForUser($user);
        return Inertia::render('Dashboard',['compromisos' => $compromisos]);
    } else {
        return redirect('/login');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('comites', ComiteController::class);
    Route::resource('tipoUsuarios', TipoUsuarioController::class);
    Route::resource('usuarios', usuarioController::class);
    Route::resource('gestionComites', GestionComiteController::class);
    Route::resource('compromisos', CompromisoController::class);
    Route::post('/generar-pdf', [PdfController::class, 'generateAndModify']);

});

require __DIR__ . '/auth.php';
