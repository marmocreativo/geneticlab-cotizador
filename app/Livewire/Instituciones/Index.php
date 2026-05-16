<?php

namespace App\Livewire\Instituciones;

use App\Models\Hospital;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroProcedencia = '';
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
        Hospital::findOrFail($this->confirmarEliminar)->delete();
        $this->confirmarEliminar = null;
        session()->flash('mensaje', 'Institución eliminada correctamente.');
    }

    public function cancelarEliminacion(): void
    {
        $this->confirmarEliminar = null;
    }

    public function render()
    {
        $hospitales = Hospital::query()
            ->withCount('medicos')
            ->when($this->busqueda, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('nombre_corto', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('ciudad', 'like', '%' . $this->busqueda . '%');
                });
            })
            ->when($this->filtroProcedencia, function ($query) {
                $query->where('procedencia', $this->filtroProcedencia);
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.instituciones.index', compact('hospitales'));
    }
}