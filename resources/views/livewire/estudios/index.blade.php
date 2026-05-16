<div>
    {{-- Encabezado --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Estudios</h1>
            <p class="text-sm text-gray-500">Catálogo de estudios moleculares y sus precios</p>
        </div>
        <a href="{{ route('estudios.create') }}" wire:navigate
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
           style="background-color:#002745;">
            + Nuevo estudio
        </a>
    </div>

    {{-- Flash --}}
    @if (session('mensaje'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('mensaje') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex-1">
            <input
                wire:model.live.debounce.300ms="busqueda"
                type="text"
                placeholder="Buscar por nombre, espécimen o área terapéutica..."
                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />
        </div>
        <select wire:model.live="filtroActivo"
                class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:w-48">
            <option value="">Todos</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estudio</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Espécimen</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Área terapéutica</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Tiempo de respuesta</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Precio unitario</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($estudios as $estudio)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $estudio->nombre }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $estudio->especimen ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $estudio->area_terapeutica ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $estudio->tiempo_respuesta ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($estudio->precio_unitario)
                                <span class="font-medium text-gray-900">
                                    ${{ number_format($estudio->precio_unitario, 2) }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($estudio->activo)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('estudios.edit', $estudio) }}" wire:navigate
                                   class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                   title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                    </svg>
                                </a>
                                <button wire:click="eliminar({{ $estudio->id }})"
                                        class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                                        title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            No se encontraron estudios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $estudios->links() }}
    </div>

    {{-- Modal confirmación eliminar --}}
    @if ($confirmarEliminar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900">¿Eliminar estudio?</h3>
                <p class="mt-1 text-sm text-gray-500">Esta acción no se puede deshacer.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cancelarEliminacion"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="confirmarEliminacion"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>