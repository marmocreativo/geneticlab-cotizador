<?php

namespace App\Livewire;

use App\Models\Cotizacion;
use App\Models\Hospital;
use App\Models\Medico;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'cotizaciones_mes'    => Cotizacion::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
            'cotizaciones_total'  => Cotizacion::count(),
            'importe_mes'         => Cotizacion::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->sum('total'),
            'importe_total'       => Cotizacion::sum('total'),
            'medicos'             => Medico::count(),
            'instituciones'       => Hospital::count(),
            'aceptadas_mes'       => Cotizacion::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->where('estado', 'aceptada')
                                        ->count(),
        ];

        $recientes = Cotizacion::with(['medico', 'hospital'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $porEstado = Cotizacion::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('livewire.dashboard', compact('stats', 'recientes', 'porEstado'));
    }
}