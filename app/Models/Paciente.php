<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folio', 'anonimo',
        'iniciales', 'nombre', 'apellido_paterno', 'apellido_materno',
        'fecha_nacimiento', 'edad', 'sexo',
        'whatsapp', 'correo', 'notas',
    ];

    protected $casts = [
        'anonimo'          => 'boolean',
        'fecha_nacimiento' => 'date',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Nombre para mostrar: iniciales, nombre completo o "Anónimo + folio"
     */
    public function getNombreDisplayAttribute(): string
    {
        if ($this->anonimo) {
            return 'Anónimo — ' . $this->folio;
        }
        /*
        if ($this->iniciales) {
            return $this->iniciales;
        }
        */

        return trim(implode(' ', array_filter([
            $this->nombre,
            $this->apellido_paterno,
            $this->apellido_materno,
        ]))) ?: $this->folio;
    }

    /**
     * Genera el folio automáticamente antes de crear
     */
    protected static function booted(): void
    {
        static::creating(function (Paciente $paciente) {
            if (empty($paciente->folio)) {
                $paciente->folio = static::generarFolio();
            }
        });
    }

    protected static function generarFolio(): string
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $year = now()->year;

            // Bloquea las filas del año en curso para evitar folios duplicados
            // por inserciones concurrentes (misma lógica que Cotizacion::generarFolio)
            $ultimo = static::withTrashed()
                ->whereYear('created_at', $year)
                ->lockForUpdate()
                ->max(\Illuminate\Support\Facades\DB::raw("CAST(SUBSTRING_INDEX(folio, '-', -1) AS UNSIGNED)"));

            $consecutivo = ($ultimo ?? 0) + 1;

            return sprintf('PAC-%d-%05d', $year, $consecutivo);
        });
    }
}