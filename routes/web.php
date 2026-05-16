<?php

use Illuminate\Support\Facades\Route;

// ── Pública ───────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::redirect('/home', '/dashboard')->name('home');

    // Cotizaciones
    Route::get('/cotizaciones',        \App\Livewire\Cotizaciones\Index::class)->name('cotizaciones.index');
    Route::get('/nueva-cotizacion',    \App\Livewire\Cotizaciones\Wizard::class)->name('cotizaciones.wizard');
    Route::get('/cotizaciones/{cotizacion}', \App\Livewire\Cotizaciones\Show::class)->name('cotizaciones.show');

    // Médicos
    Route::get('/medicos',                   \App\Livewire\Medicos\Index::class)->name('medicos.index');
    Route::get('/medicos/nuevo',             \App\Livewire\Medicos\Form::class)->name('medicos.create');
    Route::get('/medicos/{medico}/editar',   \App\Livewire\Medicos\Form::class)->name('medicos.edit');

    // Instituciones
    Route::get('/instituciones',                    \App\Livewire\Instituciones\Index::class)->name('instituciones.index');
    Route::get('/instituciones/nueva',              \App\Livewire\Instituciones\Form::class)->name('instituciones.create');
    Route::get('/instituciones/{hospital}/editar',  \App\Livewire\Instituciones\Form::class)->name('instituciones.edit');

    // Estudios
    Route::get('/estudios',                  \App\Livewire\Estudios\Index::class)->name('estudios.index');
    Route::get('/estudios/nuevo',            \App\Livewire\Estudios\Form::class)->name('estudios.create');
    Route::get('/estudios/{estudio}/editar', \App\Livewire\Estudios\Form::class)->name('estudios.edit');

    // Agenda
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/',          \App\Livewire\Agenda\Calendario::class)->name('calendario');
        Route::get('/pacientes', \App\Livewire\Agenda\Pacientes::class)->name('pacientes');
        Route::get('/centros',   \App\Livewire\Agenda\Centros::class)->name('centros');
    });

});

require __DIR__.'/settings.php';