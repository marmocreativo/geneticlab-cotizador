<?php

namespace App\Livewire\Agenda;

use App\Models\CentroAgenda;
use Livewire\Component;
use Livewire\WithPagination;

class Centros extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public bool $modalCentro = false;
    public ?int $centro_id = null;

    public string $nombre    = '';
    public string $direccion = '';
    public bool   $activo    = true;

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirModal(?int $id = null): void
    {
        $this->centro_id = $id;
        $this->nombre = $this->direccion = '';
        $this->activo = true;

        if ($id) {
            $c = CentroAgenda::findOrFail($id);
            $this->nombre    = $c->nombre;
            $this->direccion = $c->direccion ?? '';
            $this->activo    = $c->activo;
        }

        $this->modalCentro = true;
    }

    public function guardar(): void
    {
        $this->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'nullable|string|max:500',
            'activo'    => 'boolean',
        ]);

        CentroAgenda::updateOrCreate(
            ['id' => $this->centro_id],
            ['nombre' => $this->nombre, 'direccion' => $this->direccion ?: null, 'activo' => $this->activo]
        );

        $this->modalCentro = false;
    }

    public function toggleActivo(int $id): void
    {
        $centro = CentroAgenda::findOrFail($id);
        $centro->update(['activo' => !$centro->activo]);
    }

    public function eliminar(int $id): void
    {
        CentroAgenda::findOrFail($id)->delete();
    }

    public function render()
    {
        $centros = CentroAgenda::query()
            ->when($this->busqueda, fn($q) => $q->where('nombre', 'like', "%{$this->busqueda}%")
                ->orWhere('direccion', 'like', "%{$this->busqueda}%"))
            ->orderBy('nombre')
            ->paginate(20);

        return view('livewire.agenda.centros', compact('centros'));
    }
}