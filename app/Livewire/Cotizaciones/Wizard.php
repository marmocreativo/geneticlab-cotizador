<?php

namespace App\Livewire\Cotizaciones;

use App\Models\Cotizacion;
use App\Models\CotizacionEstudio;
use App\Models\Estudio;
use App\Models\Hospital;
use App\Models\Medico;
use Livewire\Component;

class Wizard extends Component
{
    public int $paso = 1;
    public int $totalPasos = 3;

    // Paso 1 — Médico
    public ?int $medico_id = null;
    public string $busquedaMedico = '';
    public bool $nuevoMedico = false;
    public string $prefijo = '';
    public string $medicoNombre = '';
    public string $medicoApellido = '';
    public string $medicoEmail = '';
    public string $medicoTelefono = '';
    public string $medicoEspecialidad = '';
    public string $medicoCedula = '';

    // Paso 2 — Institución
    public ?int $hospital_id = null;
    public string $busquedaHospital = '';
    public bool $nuevoHospital = false;
    public string $hospitalNombre = '';
    public string $hospitalProcedencia = '';
    public string $hospitalCiudad = '';

    // Paso 3 — Estudios
    public array $estudiosSeleccionados = [];
    public string $busquedaEstudio = '';
    public string $notas = '';
    public string $descuento = '0';
    public ?string $valida_hasta = null;

    // Computed helpers
    public function getMedicosProperty()
    {
        return Medico::query()
            ->when($this->busquedaMedico, fn($q) =>
                $q->where('nombre', 'like', '%' . $this->busquedaMedico . '%')
                  ->orWhere('apellido', 'like', '%' . $this->busquedaMedico . '%')
            )
            ->orderBy('nombre')
            ->limit(10)
            ->get();
    }

    public function getHospitalesProperty()
    {
        return Hospital::query()
            ->when($this->busquedaHospital, fn($q) =>
                $q->where('nombre', 'like', '%' . $this->busquedaHospital . '%')
            )
            ->orderBy('nombre')
            ->limit(10)
            ->get();
    }

    public function getEstudiosDisponiblesProperty()
    {
        $seleccionados = array_column($this->estudiosSeleccionados, 'estudio_id');

        return Estudio::query()
            ->where('activo', true)
            ->when($this->busquedaEstudio, fn($q) =>
                $q->where('nombre', 'like', '%' . $this->busquedaEstudio . '%')
                  ->orWhere('area_terapeutica', 'like', '%' . $this->busquedaEstudio . '%')
            )
            ->whereNotIn('id', $seleccionados)
            ->orderBy('nombre')
            ->limit(10)
            ->get();
    }

    public function getSubtotalProperty(): float
    {
        return collect($this->estudiosSeleccionados)->sum(fn($e) => $e['precio_unitario'] * $e['cantidad']);
    }

    public function getDescuentoImporteProperty(): float
    {
        return $this->subtotal * ((float) $this->descuento / 100);
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal - $this->descuentoImporte;
    }

    // Paso 1
    public function seleccionarMedico(int $id): void
    {
        $this->medico_id = $id;
        $this->nuevoMedico = false;
        $this->busquedaMedico = '';
    }

    public function limpiarMedico(): void
    {
        $this->medico_id = null;
    }

    public function toggleNuevoMedico(): void
    {
        $this->nuevoMedico = !$this->nuevoMedico;
        $this->medico_id = null;
    }

    // Paso 2
    public function seleccionarHospital(int $id): void
    {
        $this->hospital_id = $id;
        $this->nuevoHospital = false;
        $this->busquedaHospital = '';
    }

    public function limpiarHospital(): void
    {
        $this->hospital_id = null;
    }

    public function toggleNuevoHospital(): void
    {
        $this->nuevoHospital = !$this->nuevoHospital;
        $this->hospital_id = null;
    }

    // Paso 3
    public function agregarEstudio(int $id): void
    {
        $estudio = Estudio::find($id);
        if (!$estudio) return;

        $this->estudiosSeleccionados[] = [
            'estudio_id'     => $estudio->id,
            'nombre'         => $estudio->nombre,
            'precio_unitario' => (float) $estudio->precio_unitario,
            'cantidad'       => 1,
            'notas'          => '',
        ];

        $this->busquedaEstudio = '';
    }

    public function quitarEstudio(int $index): void
    {
        array_splice($this->estudiosSeleccionados, $index, 1);
    }

    public function actualizarCantidad(int $index, int $cantidad): void
    {
        if ($cantidad < 1) $cantidad = 1;
        $this->estudiosSeleccionados[$index]['cantidad'] = $cantidad;
    }

    // Navegación
    public function siguientePaso(): void
    {
        if ($this->paso === 1) {
            $this->validarPaso1();
        }

        if ($this->paso === 2) {
            // Institución es opcional, avanzamos siempre
        }

        if ($this->paso < $this->totalPasos) {
            $this->paso++;
        }
    }

    public function pasoAnterior(): void
    {
        if ($this->paso > 1) {
            $this->paso--;
        }
    }

    protected function validarPaso1(): void
    {
        if ($this->nuevoMedico) {
            $this->validate([
                'medicoNombre' => 'required|string|max:255',
            ], [
                'medicoNombre.required' => 'El nombre del médico es obligatorio.',
            ]);
        }
    }

    public function guardar(): void
    {
        if (empty($this->estudiosSeleccionados)) {
            $this->addError('estudios', 'Debes agregar al menos un estudio.');
            return;
        }

        // Crear médico si es nuevo
        if ($this->nuevoMedico && $this->medicoNombre) {
            $medico = Medico::create([
                'prefijo'      => $this->prefijo ?: null,
                'nombre'       => $this->medicoNombre,
                'apellido'     => $this->medicoApellido ?: null,
                'email'        => $this->medicoEmail ?: null,
                'telefono'     => $this->medicoTelefono ?: null,
                'especialidad' => $this->medicoEspecialidad ?: null,
                'cedula_profesional' => $this->medicoCedula ?: null,
                'hospital_id'  => $this->hospital_id,
            ]);
            $this->medico_id = $medico->id;
        }

        // Crear hospital si es nuevo
        if ($this->nuevoHospital && $this->hospitalNombre) {
            $hospital = Hospital::create([
                'nombre'      => $this->hospitalNombre,
                'procedencia' => $this->hospitalProcedencia ?: null,
                'ciudad'      => $this->hospitalCiudad ?: null,
            ]);
            $this->hospital_id = $hospital->id;
        }

        // Crear cotización
        $cotizacion = Cotizacion::create([
            'medico_id'   => $this->medico_id,
            'hospital_id' => $this->hospital_id,
            'estado'      => 'borrador',
            'descuento'   => (float) $this->descuento,
            'notas'       => $this->notas ?: null,
            'valida_hasta' => $this->valida_hasta ?: null,
            'subtotal'    => 0,
            'total'       => 0,
        ]);

        // Crear renglones
        foreach ($this->estudiosSeleccionados as $item) {
            CotizacionEstudio::create([
                'cotizacion_id'  => $cotizacion->id,
                'estudio_id'     => $item['estudio_id'],
                'cantidad'       => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal'       => $item['precio_unitario'] * $item['cantidad'],
                'notas'          => $item['notas'] ?: null,
            ]);
        }

        // Recalcular totales
        $cotizacion->refresh()->recalcular();

        session()->flash('mensaje', 'Cotización ' . $cotizacion->folio . ' creada correctamente.');
        $this->redirect(route('cotizaciones.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.cotizaciones.wizard');
    }
}