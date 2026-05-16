<?php

namespace App\Livewire\Estudios;

use App\Models\Estudio;
use Livewire\Component;

class Form extends Component
{
    public ?Estudio $estudio = null;

    public string $nombre = '';
    public string $especimen = '';
    public string $area_terapeutica = '';
    public string $tiempo_respuesta = '';
    public string $precio_unitario = '';
    public bool $activo = true;

    public bool $modoEdicion = false;

    protected function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:255',
            'especimen'        => 'nullable|string|max:255',
            'area_terapeutica' => 'nullable|string|max:255',
            'tiempo_respuesta' => 'nullable|string|max:100',
            'precio_unitario'  => 'nullable|numeric|min:0',
            'activo'           => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre del estudio es obligatorio.',
            'precio_unitario.numeric' => 'El precio debe ser un número válido.',
            'precio_unitario.min'    => 'El precio no puede ser negativo.',
        ];
    }

    public function mount(?Estudio $estudio = null): void
    {
        if ($estudio && $estudio->exists) {
            $this->estudio          = $estudio;
            $this->modoEdicion      = true;
            $this->nombre           = $estudio->nombre;
            $this->especimen        = $estudio->especimen ?? '';
            $this->area_terapeutica = $estudio->area_terapeutica ?? '';
            $this->tiempo_respuesta = $estudio->tiempo_respuesta ?? '';
            $this->precio_unitario  = $estudio->precio_unitario ?? '';
            $this->activo           = $estudio->activo;
        }
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        foreach (['especimen', 'area_terapeutica', 'tiempo_respuesta', 'precio_unitario'] as $campo) {
            if (isset($datos[$campo]) && $datos[$campo] === '') {
                $datos[$campo] = null;
            }
        }

        if ($this->modoEdicion) {
            $this->estudio->update($datos);
            session()->flash('mensaje', 'Estudio actualizado correctamente.');
        } else {
            Estudio::create($datos);
            session()->flash('mensaje', 'Estudio registrado correctamente.');
        }

        $this->redirect(route('estudios.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.estudios.form');
    }
}