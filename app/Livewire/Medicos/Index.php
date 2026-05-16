<?php

namespace App\Livewire\Medicos;

use App\Models\Medico;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroActivo = '';
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
        Medico::findOrFail($this->confirmarEliminar)->delete();
        $this->confirmarEliminar = null;
        session()->flash('mensaje', 'Médico eliminado correctamente.');
    }

    public function cancelarEliminacion(): void
    {
        $this->confirmarEliminar = null;
    }

    public function render()
    {
        $medicos = Medico::query()
            ->with('hospital')
            ->when($this->busqueda, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('apellido', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('email', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('especialidad', 'like', '%' . $this->busqueda . '%');
                });
            })
            ->when($this->filtroActivo !== '', function ($query) {
                $query->where('activo', (bool) $this->filtroActivo);
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.medicos.index', compact('medicos'));
    }
}