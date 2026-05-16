<div>
    {{-- Encabezado --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Resumen de actividad — {{ now()->translatedFormat('F Y') }}</p>
    </div>

    {{-- Tarjetas principales --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Cotizaciones este mes</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['cotizaciones_mes'] }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $stats['cotizaciones_total'] }} en total</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Importe cotizado (mes)</p>
            <p class="mt-1 text-3xl font-semibold" style="color:#002745;">
                ${{ number_format($stats['importe_mes'], 0) }}
            </p>
            <p class="mt-1 text-xs text-gray-400">${{ number_format($stats['importe_total'], 0) }} acumulado</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Médicos registrados</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['medicos'] }}</p>
            <a href="{{ route('medicos.index') }}" wire:navigate
               class="mt-2 inline-block text-xs font-medium hover:underline"
               style="color:#002745;">
                Ver todos →
            </a>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Instituciones</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['instituciones'] }}</p>
            <a href="{{ route('instituciones.index') }}" wire:navigate
               class="mt-2 inline-block text-xs font-medium hover:underline"
               style="color:#002745;">
                Ver todas →
            </a>
        </div>
    </div>

    {{-- Fila secundaria --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Por estado --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="mb-4 text-sm font-semibold text-gray-700">Por estado</h2>
            @php
                $estadoConfig = [
                    'borrador'  => ['Borrador',  'bg-gray-400'],
                    'enviada'   => ['Enviada',   'bg-blue-500'],
                    'aceptada'  => ['Aceptada',  'bg-green-500'],
                    'rechazada' => ['Rechazada', 'bg-red-500'],
                    'expirada'  => ['Expirada',  'bg-yellow-400'],
                ];
                $totalCots = $porEstado->sum() ?: 1;
            @endphp
            <div class="flex flex-col gap-3">
                @foreach ($estadoConfig as $key => [$label, $color])
                    @php $cant = $porEstado[$key] ?? 0; @endphp
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium text-gray-900">{{ $cant }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-gray-100">
                            <div class="h-1.5 rounded-full {{ $color }}"
                                 style="width: {{ round(($cant / $totalCots) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cotizaciones recientes --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Cotizaciones recientes</h2>
                <a href="{{ route('cotizaciones.index') }}" wire:navigate
                   class="text-xs font-medium hover:underline"
                   style="color:#002745;">
                    Ver todas
                </a>
            </div>

            @if ($recientes->isEmpty())
                <div class="py-8 text-center text-sm text-gray-400">
                    No hay cotizaciones aún.
                </div>
            @else
                <div class="flex flex-col gap-1">
                    @foreach ($recientes as $cot)
                        @php
                            $estadoBadge = [
                                'borrador'  => ['bg-gray-100',  'text-gray-600',  'Borrador'],
                                'enviada'   => ['bg-blue-50',   'text-blue-700',  'Enviada'],
                                'aceptada'  => ['bg-green-50',  'text-green-700', 'Aceptada'],
                                'rechazada' => ['bg-red-50',    'text-red-700',   'Rechazada'],
                                'expirada'  => ['bg-yellow-50', 'text-yellow-700','Expirada'],
                            ];
                            [$bg, $text, $label] = $estadoBadge[$cot->estado] ?? ['bg-gray-100', 'text-gray-600', $cot->estado];
                        @endphp
                        <a href="{{ route('cotizaciones.show', $cot) }}" wire:navigate
                           class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-xs text-gray-400">{{ $cot->folio }}</span>
                                <span class="text-sm text-gray-700">{{ $cot->medico?->nombre_completo ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-900">${{ number_format($cot->total, 0) }}</span>
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $bg }} {{ $text }}">
                                    {{ $label }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>