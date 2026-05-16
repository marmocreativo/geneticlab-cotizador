<?php

namespace App\Livewire\Cotizaciones;

use App\Models\Cotizacion;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroEstado = '';
    public ?int $confirmarEliminar = null;

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        $this->confirmarEliminar = $id;
    }

    public function confirmarEliminacion(): void
    {
        Cotizacion::findOrFail($this->confirmarEliminar)->delete();
        $this->confirmarEliminar = null;
        session()->flash('mensaje', 'Cotización eliminada correctamente.');
    }

    public function cancelarEliminacion(): void
    {
        $this->confirmarEliminar = null;
    }

    public function render()
    {
        $cotizaciones = Cotizacion::query()
            ->with(['medico', 'hospital'])
            ->when($this->busqueda, function ($query) {
                $query->where(function ($q) {
                    $q->where('folio', 'like', '%' . $this->busqueda . '%')
                      ->orWhereHas('medico', fn($q) =>
                          $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                            ->orWhere('apellido', 'like', '%' . $this->busqueda . '%')
                      )
                      ->orWhereHas('hospital', fn($q) =>
                          $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                      );
                });
            })
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.cotizaciones.index', compact('cotizaciones'));
    }
}