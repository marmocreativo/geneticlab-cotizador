<?php

namespace App\Livewire\Agenda;

use App\Models\Cita;
use App\Models\CentroAgenda;
use App\Models\Paciente;
use Carbon\Carbon;
use Livewire\Component;

class Calendario extends Component
{
    // ── Calendario ────────────────────────────────────────
    public string $vista = 'mensual';
    public string $fecha_actual;
    public ?int   $filtro_centro = null;

    // ── Wizard (nueva cita y edición) ─────────────────────
    public bool   $modalWizard = false;
    public bool   $modoEdicion = false;
    public ?int   $cita_edit_id = null;
    public int    $paso = 1;

    // Paso 1 — Paciente
    public ?int   $paciente_id_edit = null; // ID del paciente en modo edición
    public bool   $anonimo          = false;
    public string $iniciales        = '';
    public string $nombre           = '';
    public string $apellido_paterno = '';
    public string $apellido_materno = '';
    public string $fecha_nacimiento = '';
    public string $sexo             = '';
    public string $whatsapp         = '';
    public string $correo           = '';
    public string $pac_notas        = '';

    // Modo nuevo — buscar existente
    public string $paciente_modo     = 'nuevo';
    public string $busqueda_paciente = '';
    public ?int   $paciente_id_sel   = null;

    // Paso 2 — Centro
    public ?int   $centro_id_sel    = null;
    public string $filtro_estado    = '';
    public string $filtro_ciudad    = '';
    public string $busqueda_centro  = '';
    public bool   $centro_modo_nuevo = false;
    public string $centro_nombre    = '';
    public string $centro_direccion = '';

    // Paso 3 — Fecha/Hora
    public string $cita_fecha  = '';
    public string $cita_hora   = '';
    public string $cita_estado = 'programada';
    public string $cita_notas  = '';

    // ── Dialog detalle ────────────────────────────────────
    public bool  $modalDetalle  = false;
    public ?int  $detalle_id    = null;
    public string $detalle_estado = '';

    // ─────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->fecha_actual = now()->toDateString();
        $this->cita_fecha   = now()->toDateString();
    }

    // ── Navegación calendario ─────────────────────────────

    public function navegar(string $direccion): void
    {
        $fecha = Carbon::parse($this->fecha_actual);

        $this->fecha_actual = $this->vista === 'mensual'
            ? ($direccion === 'siguiente' ? $fecha->addMonth() : $fecha->subMonth())->toDateString()
            : ($direccion === 'siguiente' ? $fecha->addWeek()  : $fecha->subWeek())->toDateString();
    }

    public function hoy(): void
    {
        $this->fecha_actual = now()->toDateString();
    }

    // ── Dialog detalle ────────────────────────────────────

    public function abrirDetalle(int $id): void
    {
        $cita = Cita::findOrFail($id);
        $this->detalle_id     = $id;
        $this->detalle_estado = $cita->estado;
        $this->modalDetalle   = true;
    }

    public function guardarEstado(): void
    {
        Cita::findOrFail($this->detalle_id)->update(['estado' => $this->detalle_estado]);
    }

    public function eliminarCita(int $id): void
    {
        Cita::findOrFail($id)->delete();
        $this->modalDetalle = false;
    }

    // ── Wizard nueva cita ─────────────────────────────────

    public function abrirWizard(?string $fecha = null): void
    {
        $this->resetWizard();
        $this->cita_fecha  = $fecha ?? now()->toDateString();
        $this->modoEdicion = false;
        $this->modalWizard = true;
    }

    // ── Wizard edición ────────────────────────────────────

    public function abrirEdicion(): void
    {
        $cita    = Cita::with(['paciente', 'centro'])->findOrFail($this->detalle_id);
        $paciente = $cita->paciente;

        $this->resetWizard();
        $this->modoEdicion    = true;
        $this->cita_edit_id   = $cita->id;
        $this->modalDetalle   = false;

        // Paso 1 — datos del paciente actual
        $this->paciente_id_edit = $paciente->id;
        $this->anonimo          = $paciente->anonimo;
        $this->iniciales        = $paciente->iniciales ?? '';
        $this->nombre           = $paciente->nombre ?? '';
        $this->apellido_paterno = $paciente->apellido_paterno ?? '';
        $this->apellido_materno = $paciente->apellido_materno ?? '';
        $this->fecha_nacimiento = $paciente->fecha_nacimiento?->toDateString() ?? '';
        $this->sexo             = $paciente->sexo ?? '';
        $this->whatsapp         = $paciente->whatsapp ?? '';
        $this->correo           = $paciente->correo ?? '';
        $this->pac_notas        = $paciente->notas ?? '';

        // Paso 2 — centro actual
        $this->centro_id_sel = $cita->centro_id;

        // Paso 3 — fecha/hora/estado/notas
        $this->cita_fecha  = $cita->fecha->toDateString();
        $this->cita_hora = \Carbon\Carbon::createFromFormat('H:i:s', $cita->hora)->format('H:i');
        $this->cita_estado = $cita->estado;
        $this->cita_notas  = $cita->notas ?? '';

        $this->modalWizard = true;
    }

    // ── Navegación wizard ─────────────────────────────────

    public function siguientePaso(): void
    {
        match ($this->paso) {
            1 => $this->validarPaso1(),
            2 => $this->validarPaso2(),
        };
    }

    public function anteriorPaso(): void
    {
        if ($this->paso > 1) {
            $this->paso--;
        }
    }

    private function validarPaso1(): void
    {
        if (!$this->modoEdicion && $this->paciente_modo === 'existente') {
            $this->validate(
                ['paciente_id_sel' => 'required|exists:pacientes,id'],
                ['paciente_id_sel.required' => 'Selecciona un paciente.']
            );
        } else {
            // Siempre validar contacto (anónimo o no)
            $this->validate([
                'whatsapp' => 'nullable|required_without:correo',
                'correo'   => 'nullable|required_without:whatsapp|email',
            ], [
                'whatsapp.required_without' => 'Ingresa WhatsApp o correo.',
                'correo.required_without'   => 'Ingresa WhatsApp o correo.',
                'correo.email'              => 'El correo no es válido.',
            ]);
        }

        $this->paso = 2;
    }

    private function validarPaso2(): void
    {
        if ($this->centro_modo_nuevo) {
            $this->validate(
                ['centro_nombre' => 'required|string|max:200'],
                ['centro_nombre.required' => 'El nombre del centro es requerido.']
            );
        } else {
            $this->validate(
                ['centro_id_sel' => 'required|exists:centros_agenda,id'],
                ['centro_id_sel.required' => 'Selecciona un centro.']
            );
        }

        $this->paso = 3;
    }

    public function guardarCita(): void
    {
        $this->validate([
            'cita_fecha' => 'required|date',
            'cita_hora'  => 'required|date_format:H:i',
        ], [
            'cita_fecha.required' => 'La fecha es requerida.',
            'cita_hora.required'  => 'La hora es requerida.',
        ]);

        // ── Resolver paciente ──────────────────────────────
        if ($this->modoEdicion) {
            // Actualizar datos del paciente actual
            Paciente::findOrFail($this->paciente_id_edit)->update([
                'anonimo'          => $this->anonimo,
                'iniciales'        => $this->anonimo ? null : ($this->iniciales ?: null),
                'nombre'           => $this->anonimo ? null : ($this->nombre ?: null),
                'apellido_paterno' => $this->anonimo ? null : ($this->apellido_paterno ?: null),
                'apellido_materno' => $this->anonimo ? null : ($this->apellido_materno ?: null),
                'fecha_nacimiento' => $this->anonimo ? null : ($this->fecha_nacimiento ?: null),
                'sexo'             => $this->anonimo ? null : ($this->sexo ?: null),
                'whatsapp'         => $this->whatsapp ?: null,
                'correo'           => $this->correo ?: null,
                'notas'            => $this->pac_notas ?: null,
            ]);
            $pacienteId = $this->paciente_id_edit;
        } elseif ($this->paciente_modo === 'existente') {
            $pacienteId = $this->paciente_id_sel;
        } else {
            $paciente = Paciente::create([
                'anonimo'          => $this->anonimo,
                'iniciales'        => $this->anonimo ? null : ($this->iniciales ?: null),
                'nombre'           => $this->anonimo ? null : ($this->nombre ?: null),
                'apellido_paterno' => $this->anonimo ? null : ($this->apellido_paterno ?: null),
                'apellido_materno' => $this->anonimo ? null : ($this->apellido_materno ?: null),
                'fecha_nacimiento' => $this->anonimo ? null : ($this->fecha_nacimiento ?: null),
                'sexo'             => $this->anonimo ? null : ($this->sexo ?: null),
                'whatsapp'         => $this->whatsapp ?: null,
                'correo'           => $this->correo ?: null,
                'notas'            => $this->pac_notas ?: null,
            ]);
            $pacienteId = $paciente->id;
        }

        // ── Resolver centro ────────────────────────────────
        if ($this->centro_modo_nuevo) {
            $centro   = CentroAgenda::create([
                'nombre'    => $this->centro_nombre,
                'direccion' => $this->centro_direccion ?: null,
                'activo'    => true,
            ]);
            $centroId = $centro->id;
        } else {
            $centroId = $this->centro_id_sel;
        }

        // ── Crear o actualizar cita ────────────────────────
        Cita::updateOrCreate(
            ['id' => $this->cita_edit_id],
            [
                'paciente_id' => $pacienteId,
                'centro_id'   => $centroId,
                'fecha'       => $this->cita_fecha,
                'hora'        => $this->cita_hora,
                'estado'      => $this->cita_estado,
                'notas'       => $this->cita_notas ?: null,
            ]
        );

        $this->modalWizard = false;
        $this->resetWizard();
    }

    private function resetWizard(): void
    {
        $this->paso              = 1;
        $this->modoEdicion       = false;
        $this->cita_edit_id      = null;
        $this->paciente_id_edit  = null;
        $this->paciente_modo     = 'nuevo';
        $this->busqueda_paciente = '';
        $this->paciente_id_sel   = null;
        $this->anonimo           = false;
        $this->iniciales = $this->nombre = $this->apellido_paterno = '';
        $this->apellido_materno = $this->fecha_nacimiento = $this->sexo = '';
        $this->whatsapp = $this->correo = $this->pac_notas = '';
        $this->centro_id_sel     = null;
        $this->filtro_estado     = '';
        $this->filtro_ciudad     = '';
        $this->busqueda_centro   = '';
        $this->centro_modo_nuevo = false;
        $this->centro_nombre     = '';
        $this->centro_direccion  = '';
        $this->cita_hora         = '';
        $this->cita_estado       = 'programada';
        $this->cita_notas        = '';
    }

    // ── Computed properties ───────────────────────────────

    public function getCitasDelPeriodoProperty()
    {
        $fecha = Carbon::parse($this->fecha_actual);

        [$inicio, $fin] = $this->vista === 'mensual'
            ? [$fecha->copy()->startOfMonth(), $fecha->copy()->endOfMonth()]
            : [$fecha->copy()->startOfWeek(Carbon::MONDAY), $fecha->copy()->endOfWeek(Carbon::SUNDAY)];

        return Cita::with(['paciente', 'centro'])
            ->whereBetween('fecha', [$inicio, $fin])
            ->when($this->filtro_centro, fn($q) => $q->where('centro_id', $this->filtro_centro))
            ->orderBy('fecha')->orderBy('hora')
            ->get()
            ->groupBy(fn($c) => $c->fecha->toDateString());
    }

    public function getPacientesBusquedaProperty()
    {
        if (strlen($this->busqueda_paciente) < 2) return collect();

        return Paciente::where('folio', 'like', "%{$this->busqueda_paciente}%")
            ->orWhere('nombre', 'like', "%{$this->busqueda_paciente}%")
            ->orWhere('iniciales', 'like', "%{$this->busqueda_paciente}%")
            ->orWhere('whatsapp', 'like', "%{$this->busqueda_paciente}%")
            ->orWhere('correo', 'like', "%{$this->busqueda_paciente}%")
            ->limit(8)->get();
    }

    public function getCentrosFiltradosProperty()
    {
        return CentroAgenda::where('activo', true)
            ->when($this->busqueda_centro, fn($q) => $q->where('nombre', 'like', "%{$this->busqueda_centro}%"))
            ->when($this->filtro_estado,   fn($q) => $q->where('direccion', 'like', "%{$this->filtro_estado}%"))
            ->when($this->filtro_ciudad,   fn($q) => $q->where('direccion', 'like', "%{$this->filtro_ciudad}%"))
            ->orderBy('nombre')->get();
    }

    public function getEstadosProperty()
    {
        return CentroAgenda::where('activo', true)->whereNotNull('direccion')->get()
            ->map(fn($c) => $this->extraerEstado($c->direccion))
            ->filter()->unique()->sort()->values();
    }

    public function getCiudadesProperty()
    {
        return CentroAgenda::where('activo', true)->whereNotNull('direccion')
            ->when($this->filtro_estado, fn($q) => $q->where('direccion', 'like', "%{$this->filtro_estado}%"))
            ->get()
            ->map(fn($c) => $this->extraerCiudad($c->direccion))
            ->filter()->unique()->sort()->values();
    }

    private function extraerEstado(string $dir): ?string
    {
        $partes = array_map('trim', explode(',', $dir));
        $count  = count($partes);
        if ($count < 2) return null;
        return preg_replace('/\s+\d{5}$/', '', $partes[$count - 1]) ?: null;
    }

    private function extraerCiudad(string $dir): ?string
    {
        $partes = array_map('trim', explode(',', $dir));
        $count  = count($partes);
        if ($count < 3) return null;
        return $partes[$count - 2] ?: null;
    }

    public function getDetalleCitaProperty(): ?Cita
    {
        if (!$this->detalle_id) return null;
        return Cita::with(['paciente', 'centro'])->find($this->detalle_id);
    }

    // ── Render ────────────────────────────────────────────

    public function render()
    {
        $fecha = Carbon::parse($this->fecha_actual);

        return view('livewire.agenda.calendario', [
            'citasPorDia'       => $this->citasDelPeriodo,
            'centros'           => CentroAgenda::where('activo', true)->orderBy('nombre')->get(),
            'periodo'           => $fecha,
            'diasMes'           => $this->vista === 'mensual' ? $this->getDiasMes($fecha) : null,
            'diasSemana'        => $this->vista === 'semanal' ? $this->getDiasSemana($fecha) : null,
            'pacientesBusqueda' => $this->pacientesBusqueda,
            'centrosFiltrados'  => $this->centrosFiltrados,
            'estados'           => $this->estados,
            'ciudades'          => $this->ciudades,
            'detalleCita'       => $this->detalleCita,
        ]);
    }

    private function getDiasMes(Carbon $fecha): array
    {
        $inicio = $fecha->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $fin    = $fecha->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $dias   = [];
        $cursor = $inicio->copy();
        while ($cursor <= $fin) { $dias[] = $cursor->copy(); $cursor->addDay(); }
        return $dias;
    }

    private function getDiasSemana(Carbon $fecha): array
    {
        $inicio = $fecha->copy()->startOfWeek(Carbon::MONDAY);
        $dias   = [];
        for ($i = 0; $i < 7; $i++) { $dias[] = $inicio->copy()->addDays($i); }
        return $dias;
    }
}