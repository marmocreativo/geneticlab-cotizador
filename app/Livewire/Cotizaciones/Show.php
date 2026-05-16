<?php

namespace App\Livewire\Cotizaciones;

use App\Models\Cotizacion;
use Livewire\Component;

class Show extends Component
{
    public Cotizacion $cotizacion;

    public function mount(Cotizacion $cotizacion): void
    {
        $this->cotizacion = $cotizacion->load(['medico.hospital', 'hospital', 'estudios.estudio']);
    }

    public function cambiarEstado(string $estado): void
    {
        $this->cotizacion->update(['estado' => $estado]);
        $this->cotizacion->refresh();
        session()->flash('mensaje', 'Estado actualizado correctamente.');
    }

    public function render()
    {
        return view('livewire.cotizaciones.show');
    }
}