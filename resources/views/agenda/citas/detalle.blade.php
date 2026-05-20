@php
    $colores = [
        'programada' => 'bg-blue-100 text-blue-800',
        'confirmada' => 'bg-green-100 text-green-800',
        'realizada'  => 'bg-gray-200 text-gray-600',
        'cancelada'  => 'bg-red-100 text-red-700',
    ];
    $textoWa = urlencode(
        "Hola {$cita->paciente->nombre_display}, le confirmamos su cita el " .
        $cita->fecha->translatedFormat('l d \d\e F') .
        " a las {$cita->hora}."
    );
@endphp

<div class="p-6 space-y-4">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Detalle de cita</p>
            <h2 class="text-lg font-semibold text-gray-900 mt-0.5">
                {{ $cita->paciente->nombre_display }}
            </h2>
        </div>
        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $colores[$cita->estado] ?? 'bg-gray-100 text-gray-600' }}">
            {{ ucfirst($cita->estado) }}
        </span>
    </div>

    {{-- Datos --}}
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
            <p class="text-xs text-gray-400">Fecha</p>
            <p class="font-medium text-gray-900">{{ $cita->fecha->translatedFormat('l d \d\e F \d\e Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Hora</p>
            <p class="font-medium text-gray-900">{{ $cita->hora }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-xs text-gray-400">Centro</p>
            <p class="font-medium text-gray-900">{{ $cita->centro->nombre }}</p>
            @if($cita->centro->direccion)
                <p class="text-xs text-gray-400 mt-0.5">{{ $cita->centro->direccion }}</p>
            @endif
        </div>
        @if($cita->notas)
            <div class="col-span-2">
                <p class="text-xs text-gray-400">Notas</p>
                <p class="text-gray-700">{{ $cita->notas }}</p>
            </div>
        @endif
    </div>

    {{-- Cambiar estado --}}
    <div>
        <p class="text-xs text-gray-400 mb-1">Cambiar estado</p>
        <form method="POST" action="/agenda/citas/{{ $cita->id }}/estado"
              class="flex gap-2 flex-wrap">
            @csrf
            @method('PATCH')
            <select name="estado"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="programada" @selected($cita->estado === 'programada')>Programada</option>
                <option value="confirmada" @selected($cita->estado === 'confirmada')>Confirmada</option>
                <option value="realizada"  @selected($cita->estado === 'realizada')>Realizada</option>
                <option value="cancelada"  @selected($cita->estado === 'cancelada')>Cancelada</option>
            </select>
            <button type="submit"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                Guardar
            </button>
        </form>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
        <div class="flex gap-2">
            @if($cita->paciente->whatsapp)
                <a href="https://wa.me/52{{ $cita->paciente->whatsapp }}?text={{ $textoWa }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    WhatsApp
                </a>
            @endif
        </div>

        <form method="POST" action="/agenda/citas/{{ $cita->id }}"
              onsubmit="return confirm('¿Eliminar esta cita?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                Eliminar
            </button>
        </form>
    </div>
</div>