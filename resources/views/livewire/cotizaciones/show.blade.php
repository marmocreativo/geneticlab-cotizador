<div>
    {{-- Encabezado --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('cotizaciones.index') }}" wire:navigate
               class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $cotizacion->folio }}</h1>
                <p class="text-sm text-gray-500">{{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Acciones de estado --}}
        <div class="flex items-center gap-2">
            @if ($cotizacion->estado === 'borrador')
                <button wire:click="cambiarEstado('enviada')"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Marcar como enviada
                </button>
            @elseif ($cotizacion->estado === 'enviada')
                <button wire:click="cambiarEstado('aceptada')"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Marcar como aceptada
                </button>
                <button wire:click="cambiarEstado('rechazada')"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                    Rechazada
                </button>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if (session('mensaje'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('mensaje') }}
        </div>
    @endif

    {{-- Tarjetas de info general --}}
    @php
        $estadoConfig = [
            'borrador'  => ['bg-gray-100',  'text-gray-600',  'Borrador'],
            'enviada'   => ['bg-blue-50',   'text-blue-700',  'Enviada'],
            'aceptada'  => ['bg-green-50',  'text-green-700', 'Aceptada'],
            'rechazada' => ['bg-red-50',    'text-red-700',   'Rechazada'],
            'expirada'  => ['bg-yellow-50', 'text-yellow-700','Expirada'],
        ];
        [$bgEstado, $textEstado, $labelEstado] = $estadoConfig[$cotizacion->estado] ?? ['bg-gray-100', 'text-gray-600', $cotizacion->estado];
    @endphp

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Estado</p>
            <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $bgEstado }} {{ $textEstado }}">
                {{ $labelEstado }}
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Médico</p>
            <p class="mt-1 font-medium text-gray-900">{{ $cotizacion->medico?->nombre_completo ?? '—' }}</p>
            @if ($cotizacion->medico?->especialidad)
                <p class="text-sm text-gray-400">{{ $cotizacion->medico->especialidad }}</p>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Institución</p>
            <p class="mt-1 font-medium text-gray-900">{{ $cotizacion->hospital?->nombre ?? '—' }}</p>
            @if ($cotizacion->hospital?->procedencia)
                <span class="mt-1 inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                    {{ $cotizacion->hospital->procedencia }}
                </span>
            @endif
        </div>
    </div>

    {{-- Tabla de estudios --}}
    <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Estudios solicitados</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estudio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Cantidad</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Precio unitario</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($cotizacion->estudios as $renglon)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $renglon->estudio?->nombre ?? '—' }}</div>
                            @if ($renglon->notas)
                                <div class="text-xs text-gray-400">{{ $renglon->notas }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $renglon->cantidad }}</td>
                        <td class="px-4 py-3 text-gray-600">${{ number_format($renglon->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900">${{ number_format($renglon->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totales --}}
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col items-end gap-1">
                <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($cotizacion->subtotal, 2) }}</span>
                </div>
                @if ($cotizacion->descuento > 0)
                    <div class="flex w-full max-w-xs justify-between text-sm">
                        <span class="text-gray-600">Descuento ({{ $cotizacion->descuento }}%)</span>
                        <span class="text-red-500">-${{ number_format($cotizacion->subtotal * ($cotizacion->descuento / 100), 2) }}</span>
                    </div>
                @endif
                <div class="flex w-full max-w-xs justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900">
                    <span>Total</span>
                    <span>${{ number_format($cotizacion->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notas y vigencia --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @if ($cotizacion->notas)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Notas</p>
                <p class="mt-2 text-sm text-gray-700">{{ $cotizacion->notas }}</p>
            </div>
        @endif

        @if ($cotizacion->valida_hasta)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Vigencia</p>
                <p class="mt-2 text-sm text-gray-700">Válida hasta el {{ $cotizacion->valida_hasta->format('d/m/Y') }}</p>
            </div>
        @endif
    </div>
</div>