<?php

namespace App\Livewire\Agenda;

use App\Models\Paciente;
use Livewire\Component;
use Livewire\WithPagination;

class Pacientes extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public bool $modalPaciente = false;
    public ?int $paciente_id = null;

    // Formulario
    public bool   $anonimo          = false;
    public string $iniciales        = '';
    public string $nombre           = '';
    public string $apellido_paterno = '';
    public string $apellido_materno = '';
    public string $fecha_nacimiento = '';
    public string $sexo             = '';
    public string $whatsapp         = '';
    public string $correo           = '';
    public string $notas            = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->paciente_id = $id;

        if ($id) {
            $p = Paciente::findOrFail($id);
            $this->anonimo          = $p->anonimo;
            $this->iniciales        = $p->iniciales ?? '';
            $this->nombre           = $p->nombre ?? '';
            $this->apellido_paterno = $p->apellido_paterno ?? '';
            $this->apellido_materno = $p->apellido_materno ?? '';
            $this->fecha_nacimiento = $p->fecha_nacimiento?->toDateString() ?? '';
            $this->sexo             = $p->sexo ?? '';
            $this->whatsapp         = $p->whatsapp ?? '';
            $this->correo           = $p->correo ?? '';
            $this->notas            = $p->notas ?? '';
        }

        $this->modalPaciente = true;
    }

    public function guardar(): void
    {
        $rules = [
            'anonimo'   => 'boolean',
            'iniciales' => 'nullable|string|max:10',
            'nombre'    => 'nullable|string|max:100',
            'whatsapp'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:150',
            'sexo'      => 'nullable|in:M,F,otro',
        ];

        // Al menos un medio de contacto si no es anónimo
        if (!$this->anonimo) {
            $rules['whatsapp'] = 'nullable|required_without:correo|string|max:20';
            $rules['correo']   = 'nullable|required_without:whatsapp|email|max:150';
        }

        $this->validate($rules);

        $datos = [
            'anonimo'          => $this->anonimo,
            'iniciales'        => $this->anonimo ? null : ($this->iniciales ?: null),
            'nombre'           => $this->anonimo ? null : ($this->nombre ?: null),
            'apellido_paterno' => $this->anonimo ? null : ($this->apellido_paterno ?: null),
            'apellido_materno' => $this->anonimo ? null : ($this->apellido_materno ?: null),
            'fecha_nacimiento' => $this->anonimo ? null : ($this->fecha_nacimiento ?: null),
            'sexo'             => $this->anonimo ? null : ($this->sexo ?: null),
            'whatsapp'         => $this->anonimo ? null : ($this->whatsapp ?: null),
            'correo'           => $this->anonimo ? null : ($this->correo ?: null),
            'notas'            => $this->notas ?: null,
        ];

        Paciente::updateOrCreate(['id' => $this->paciente_id], $datos);

        $this->modalPaciente = false;
    }

    public function eliminar(int $id): void
    {
        Paciente::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->paciente_id = null;
        $this->anonimo = false;
        $this->iniciales = $this->nombre = $this->apellido_paterno = '';
        $this->apellido_materno = $this->fecha_nacimiento = $this->sexo = '';
        $this->whatsapp = $this->correo = $this->notas = '';
    }

    public function render()
    {
        $pacientes = Paciente::query()
            ->when($this->busqueda, function ($q) {
                $q->where('folio', 'like', "%{$this->busqueda}%")
                  ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                  ->orWhere('iniciales', 'like', "%{$this->busqueda}%")
                  ->orWhere('whatsapp', 'like', "%{$this->busqueda}%")
                  ->orWhere('correo', 'like', "%{$this->busqueda}%");
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.agenda.pacientes', compact('pacientes'));
    }
}