<?php

namespace App\Livewire\Instituciones;

use App\Models\Hospital;
use Livewire\Component;

class Form extends Component
{
    public ?Hospital $hospital = null;

    public string $nombre = '';
    public string $nombre_corto = '';
    public string $procedencia = '';
    public string $direccion = '';
    public string $ciudad = '';
    public string $estado = '';
    public string $telefono = '';
    public string $email = '';
    public string $notas = '';

    public bool $modoEdicion = false;

    protected function rules(): array
    {
        return [
            'nombre'       => 'required|string|max:255',
            'nombre_corto' => 'nullable|string|max:50',
            'procedencia'  => 'nullable|in:PRIVADO,IMSS,ISSSTE,SSA',
            'direccion'    => 'nullable|string|max:255',
            'ciudad'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:100',
            'telefono'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'notas'        => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.email'     => 'Ingresa un correo válido.',
        ];
    }

    public function mount(?Hospital $hospital = null): void
    {
        if ($hospital && $hospital->exists) {
            $this->hospital      = $hospital;
            $this->modoEdicion   = true;
            $this->nombre        = $hospital->nombre;
            $this->nombre_corto  = $hospital->nombre_corto ?? '';
            $this->procedencia   = $hospital->procedencia ?? '';
            $this->direccion     = $hospital->direccion ?? '';
            $this->ciudad        = $hospital->ciudad ?? '';
            $this->estado        = $hospital->estado ?? '';
            $this->telefono      = $hospital->telefono ?? '';
            $this->email         = $hospital->email ?? '';
            $this->notas         = $hospital->notas ?? '';
        }
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        foreach (['nombre_corto', 'procedencia', 'direccion', 'ciudad', 'estado', 'telefono', 'email', 'notas'] as $campo) {
            if (isset($datos[$campo]) && $datos[$campo] === '') {
                $datos[$campo] = null;
            }
        }

        if ($this->modoEdicion) {
            $this->hospital->update($datos);
            session()->flash('mensaje', 'Institución actualizada correctamente.');
        } else {
            Hospital::create($datos);
            session()->flash('mensaje', 'Institución registrada correctamente.');
        }

        $this->redirect(route('instituciones.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.instituciones.form');
    }
}