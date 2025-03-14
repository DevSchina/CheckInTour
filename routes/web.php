<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\TuristaController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified']) // Aplicar middleware
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/planillas', [PlanillaController::class, 'index'])->name('planillas.index');
    Route::get('/planillas/create', [PlanillaController::class, 'create'])->name('planillas.create');
    Route::post('/planillas', [PlanillaController::class, 'store'])->name('planillas.store');
    Route::get('/planillas/{id}', [PlanillaController::class, 'show'])->name('planillas.show');

    Route::get('/planillas/{id}/imprimir', [PlanillaController::class, 'imprimir'])->name('planillas.imprimir');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('planillas', PlanillaController::class);
    Route::post('planillas/{planilla}/turistas', [TuristaController::class, 'store'])->name('turistas.store');
    Route::put('/turistas/{turista}/actualizar-asistencia', [TuristaController::class, 'actualizarAsistencia'])
    ->name('turistas.actualizarAsistencia');
});Route::put('/turistas/{turista}/asistencia', [TuristaController::class, 'actualizarAsistencia'])
->name('turistas.actualizarAsistencia');


/*Route::get('/turistas/{turista}/edit', [TuristaController::class, 'edit'])
    ->name('turistas.edit')
    ->middleware(['auth']);*/

Route::get('/planillas/{planilla}/turistas/{turista}/edit', [TuristaController::class, 'edit'])->name('turistas.edit')->middleware(['auth']);


/*Route::put('/turistas/{turista}', [TuristaController::class, 'update'])
    ->name('turistas.update')
    ->middleware(['auth']);*/

Route::put('/planillas/{planilla}/turistas/{turista}', [TuristaController::class, 'update'])->name('turistas.update')->middleware(['auth']);


Route::delete('/turistas/{turista}', [TuristaController::class, 'destroy'])
    ->name('turistas.destroy')
    ->middleware(['auth']);

Route::post('/planillas/{planilla}/finalizar', [PlanillaController::class, 'finalizar'])->name('planillas.finalizar');


Route::post('/planillas/{planilla}/cargar-excel', [PlanillaController::class, 'cargarExcel'])->name('planillas.cargarExcel');
require __DIR__.'/auth.php';
