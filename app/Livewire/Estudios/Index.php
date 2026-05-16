<?php

namespace App\Livewire\Estudios;

use App\Models\Estudio;
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
        Estudio::findOrFail($this->confirmarEliminar)->delete();
        $this->confirmarEliminar = null;
        session()->flash('mensaje', 'Estudio eliminado correctamente.');
    }

    public function cancelarEliminacion(): void
    {
        $this->confirmarEliminar = null;
    }

    public function render()
    {
        $estudios = Estudio::query()
            ->when($this->busqueda, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('especimen', 'like', '%' . $this->busqueda . '%')
                      ->orWhere('area_terapeutica', 'like', '%' . $this->busqueda . '%');
                });
            })
            ->when($this->filtroActivo !== '', function ($query) {
                $query->where('activo', (bool) $this->filtroActivo);
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('livewire.estudios.index', compact('estudios'));
    }
}