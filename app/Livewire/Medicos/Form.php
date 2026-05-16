<?php

namespace App\Livewire\Medicos;

use App\Models\Hospital;
use App\Models\Medico;
use Livewire\Component;

class Form extends Component
{
    public ?Medico $medico = null;

    public string $prefijo = '';
    public string $nombre = '';
    public string $apellido = '';
    public string $email = '';
    public string $telefono = '';
    public string $especialidad = '';
    public string $cedula_profesional = '';
    public string $notas = '';
    public bool $activo = true;
    public ?int $hospital_id = null;

    public bool $modoEdicion = false;

    protected function rules(): array
    {
        return [
            'nombre'            => 'required|string|max:255',
            'prefijo'           => 'nullable|in:Dr.,Dra.',
            'apellido'          => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'telefono'          => 'nullable|string|max:50',
            'especialidad'      => 'nullable|string|max:255',
            'cedula_profesional'=> 'nullable|string|max:100',
            'notas'             => 'nullable|string',
            'activo'            => 'boolean',
            'hospital_id'       => 'nullable|exists:hospitales,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.email'     => 'Ingresa un correo válido.',
            'hospital_id.exists' => 'La institución seleccionada no existe.',
        ];
    }

    public function mount(?Medico $medico = null): void
    {
        if ($medico && $medico->exists) {
            $this->medico          = $medico;
            $this->modoEdicion     = true;
            $this->prefijo         = $medico->prefijo ?? '';
            $this->nombre          = $medico->nombre;
            $this->apellido        = $medico->apellido ?? '';
            $this->email           = $medico->email ?? '';
            $this->telefono        = $medico->telefono ?? '';
            $this->especialidad    = $medico->especialidad ?? '';
            $this->cedula_profesional = $medico->cedula_profesional ?? '';
            $this->notas           = $medico->notas ?? '';
            $this->activo          = $medico->activo;
            $this->hospital_id     = $medico->hospital_id;
        }
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        // Convertir strings vacíos a null
        foreach (['prefijo', 'apellido', 'email', 'telefono', 'especialidad', 'cedula_profesional', 'notas'] as $campo) {
            if (isset($datos[$campo]) && $datos[$campo] === '') {
                $datos[$campo] = null;
            }
        }

        if ($this->modoEdicion) {
            $this->medico->update($datos);
            session()->flash('mensaje', 'Médico actualizado correctamente.');
        } else {
            Medico::create($datos);
            session()->flash('mensaje', 'Médico registrado correctamente.');
        }

        $this->redirect(route('medicos.index'), navigate: true);
    }

    public function render()
    {
        $hospitales = Hospital::orderBy('nombre')->get(['id', 'nombre']);

        return view('livewire.medicos.form', compact('hospitales'));
    }
}