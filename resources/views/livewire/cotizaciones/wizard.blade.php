<div>
    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('cotizaciones.index') }}" wire:navigate
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nueva cotización</h1>
            <p class="text-sm text-gray-500">Paso {{ $paso }} de {{ $totalPasos }}</p>
        </div>
    </div>

    {{-- Indicador de pasos --}}
    <div class="mb-8 flex items-center gap-2">
        @foreach (['Médico', 'Institución', 'Estudios'] as $i => $label)
            @php $num = $i + 1; @endphp
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold
                    {{ $paso === $num ? 'text-white' : ($paso > $num ? 'text-white' : 'bg-gray-100 text-gray-400') }}"
                    style="{{ $paso === $num ? 'background-color:#002745;' : ($paso > $num ? 'background-color:#F3A632;' : '') }}">
                    @if ($paso > $num) ✓ @else {{ $num }} @endif
                </div>
                <span class="text-sm {{ $paso === $num ? 'font-medium text-gray-900' : 'text-gray-400' }}">
                    {{ $label }}
                </span>
            </div>
            @if ($num < $totalPasos)
                <div class="flex-1 h-px bg-gray-200 mx-1"></div>
            @endif
        @endforeach
    </div>

    {{-- ── PASO 1: MÉDICO ── --}}
    @if ($paso === 1)
        <div class="rounded-xl border border-gray-200 bg-white p-6 max-w-2xl">
            <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-700">Selecciona o registra el médico</h2>
            <p class="mb-4 text-sm text-gray-400">Busca un médico existente o registra uno nuevo.</p>

            @if (!$medico_id && !$nuevoMedico)
                <input
                    wire:model.live.debounce.300ms="busquedaMedico"
                    type="text"
                    placeholder="Buscar médico por nombre..."
                    class="mb-3 w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />

                @if ($busquedaMedico)
                    <div class="mb-3 flex flex-col gap-1">
                        @forelse ($this->medicos as $m)
                            <button wire:click="seleccionarMedico({{ $m->id }})"
                                    class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $m->nombre_completo }}</span>
                                    @if ($m->especialidad)
                                        <span class="ml-2 text-sm text-gray-400">{{ $m->especialidad }}</span>
                                    @endif
                                </div>
                                @if ($m->hospital)
                                    <span class="text-xs text-gray-400">{{ $m->hospital->nombre }}</span>
                                @endif
                            </button>
                        @empty
                            <p class="text-sm text-gray-400 px-1">No se encontraron médicos.</p>
                        @endforelse
                    </div>
                @endif

                <button wire:click="toggleNuevoMedico"
                        class="inline-flex items-center gap-1 text-sm font-medium hover:underline"
                        style="color:#002745;">
                    + Registrar nuevo médico
                </button>
            @endif

            @if ($medico_id)
                @php $medicoSel = \App\Models\Medico::find($medico_id); @endphp
                <div class="flex items-center justify-between rounded-lg border px-4 py-3" style="border-color:#002745; background-color:#f0f4f8;">
                    <div>
                        <p class="font-medium text-gray-900">{{ $medicoSel?->nombre_completo }}</p>
                        @if ($medicoSel?->especialidad)
                            <p class="text-sm text-gray-400">{{ $medicoSel->especialidad }}</p>
                        @endif
                    </div>
                    <button wire:click="limpiarMedico" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if ($nuevoMedico)
                <div class="flex flex-col gap-4 mt-2">
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                            <select wire:model="prefijo"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">—</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Dra.">Dra.</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                            <input wire:model="medicoNombre" type="text" placeholder="Nombre(s)"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            @error('medicoNombre')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Apellido</label>
                            <input wire:model="medicoApellido" type="text" placeholder="Apellidos"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input wire:model="medicoEmail" type="email"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                            <input wire:model="medicoTelefono" type="text"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Especialidad</label>
                            <input wire:model="medicoEspecialidad" type="text"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Cédula</label>
                            <input wire:model="medicoCedula" type="text"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>
                    <button wire:click="toggleNuevoMedico" class="text-sm text-gray-400 hover:text-gray-600 text-left">
                        Cancelar nuevo médico
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ── PASO 2: INSTITUCIÓN ── --}}
    @if ($paso === 2)
        <div class="rounded-xl border border-gray-200 bg-white p-6 max-w-2xl">
            <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-700">Institución</h2>
            <p class="mb-4 text-sm text-gray-400">Este campo es opcional.</p>

            @if (!$hospital_id && !$nuevoHospital)
                <input
                    wire:model.live.debounce.300ms="busquedaHospital"
                    type="text"
                    placeholder="Buscar institución..."
                    class="mb-3 w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />

                @if ($busquedaHospital)
                    <div class="mb-3 flex flex-col gap-1">
                        @forelse ($this->hospitales as $h)
                            <button wire:click="seleccionarHospital({{ $h->id }})"
                                    class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                                <span class="font-medium text-gray-900">{{ $h->nombre }}</span>
                                @if ($h->procedencia)
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                        {{ $h->procedencia }}
                                    </span>
                                @endif
                            </button>
                        @empty
                            <p class="text-sm text-gray-400 px-1">No se encontraron instituciones.</p>
                        @endforelse
                    </div>
                @endif

                <button wire:click="toggleNuevoHospital"
                        class="inline-flex items-center gap-1 text-sm font-medium hover:underline"
                        style="color:#002745;">
                    + Registrar nueva institución
                </button>
            @endif

            @if ($hospital_id)
                @php $hospSel = \App\Models\Hospital::find($hospital_id); @endphp
                <div class="flex items-center justify-between rounded-lg border px-4 py-3" style="border-color:#002745; background-color:#f0f4f8;">
                    <div>
                        <p class="font-medium text-gray-900">{{ $hospSel?->nombre }}</p>
                        @if ($hospSel?->procedencia)
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                {{ $hospSel->procedencia }}
                            </span>
                        @endif
                    </div>
                    <button wire:click="limpiarHospital" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if ($nuevoHospital)
                <div class="flex flex-col gap-4 mt-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                        <input wire:model="hospitalNombre" type="text" placeholder="Nombre de la institución"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Procedencia</label>
                            <select wire:model="hospitalProcedencia"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Sin especificar</option>
                                <option value="PRIVADO">Privado</option>
                                <option value="IMSS">IMSS</option>
                                <option value="ISSSTE">ISSSTE</option>
                                <option value="SSA">SSA</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Ciudad</label>
                            <input wire:model="hospitalCiudad" type="text"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>
                    <button wire:click="toggleNuevoHospital" class="text-sm text-gray-400 hover:text-gray-600 text-left">
                        Cancelar nueva institución
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ── PASO 3: ESTUDIOS ── --}}
    @if ($paso === 3)
        <div class="rounded-xl border border-gray-200 bg-white p-6 max-w-2xl">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Agrega los estudios</h2>

            <input
                wire:model.live.debounce.300ms="busquedaEstudio"
                type="text"
                placeholder="Buscar estudio por nombre o área..."
                class="mb-3 w-full rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />

            @if ($busquedaEstudio)
                <div class="mb-4 flex flex-col gap-1">
                    @forelse ($this->estudiosDisponibles as $e)
                        <button wire:click="agregarEstudio({{ $e->id }})"
                                class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                            <div>
                                <span class="font-medium text-gray-900">{{ $e->nombre }}</span>
                                @if ($e->area_terapeutica)
                                    <span class="ml-2 text-xs text-gray-400">{{ $e->area_terapeutica }}</span>
                                @endif
                            </div>
                            <span class="text-sm font-medium" style="color:#002745;">
                                ${{ number_format($e->precio_unitario, 2) }}
                            </span>
                        </button>
                    @empty
                        <p class="text-sm text-gray-400 px-1">No se encontraron estudios.</p>
                    @endforelse
                </div>
            @endif

            {{-- Estudios seleccionados --}}
            @if (count($estudiosSeleccionados))
                <div class="mb-4 flex flex-col gap-2">
                    @foreach ($estudiosSeleccionados as $i => $item)
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3">
                            <div class="flex-1">
                                <p class="font-medium text-sm text-gray-900">{{ $item['nombre'] }}</p>
                                <p class="text-xs text-gray-400">${{ number_format($item['precio_unitario'], 2) }} c/u</p>
                            </div>
                            <div class="w-20">
                                <input
                                    type="number"
                                    min="1"
                                    value="{{ $item['cantidad'] }}"
                                    wire:change="actualizarCantidad({{ $i }}, $event.target.value)"
                                    class="w-full rounded-lg border border-gray-200 px-2 py-1 text-center text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                />
                            </div>
                            <span class="w-24 text-right text-sm font-medium text-gray-900">
                                ${{ number_format($item['precio_unitario'] * $item['cantidad'], 2) }}
                            </span>
                            <button wire:click="quitarEstudio({{ $i }})"
                                    class="rounded-lg p-1 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Totales --}}
                <div class="mb-4 flex flex-col items-end gap-1 border-t border-gray-200 pt-4">
                    <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    @if ((float)$descuento > 0)
                        <div class="flex w-full max-w-xs justify-between text-sm">
                            <span class="text-gray-600">Descuento ({{ $descuento }}%)</span>
                            <span class="text-red-500">-${{ number_format($this->descuentoImporte, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex w-full max-w-xs justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900">
                        <span>Total</span>
                        <span>${{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
            @endif

            @error('estudios')
                <p class="mb-4 text-sm text-red-500">{{ $message }}</p>
            @enderror

            {{-- Opciones adicionales --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border-t border-gray-200 pt-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Descuento (%)</label>
                    <input wire:model.live="descuento" type="number" min="0" max="100" step="0.5" placeholder="0"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Válida hasta</label>
                    <input wire:model="valida_hasta" type="date"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                <textarea wire:model="notas" rows="2" placeholder="Observaciones generales..."
                          class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>
        </div>
    @endif

    {{-- Navegación --}}
    <div class="mt-6 flex items-center justify-between max-w-2xl">
        <button wire:click="pasoAnterior"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-40"
                @if($paso === 1) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Anterior
        </button>

        @if ($paso < $totalPasos)
            <button wire:click="siguientePaso"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                Siguiente
            </button>
        @else
            <button wire:click="guardar"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                Crear cotización
            </button>
        @endif
    </div>
</div>