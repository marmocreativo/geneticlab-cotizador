<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Filtro de fechas --}}
        <div class="flex flex-wrap items-end gap-3">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Desde</label>
                    <input
                        type="date"
                        name="desde"
                        value="{{ $desde->toDateString() }}"
                        class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-800 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Hasta</label>
                    <input
                        type="date"
                        name="hasta"
                        value="{{ $hasta->toDateString() }}"
                        class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-800 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                </div>
                <flux:button type="submit" variant="primary">Filtrar</flux:button>
            </form>
        </div>

        {{-- Tarjetas de totales --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Cotizaciones en el período</p>
                <p class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $totalCotizaciones }}
                </p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Citas en el período</p>
                <p class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $totalCitas }}
                </p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
        </div>

        {{-- Gráfica de serie temporal --}}
        <div
            class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
            x-data="{
                agrupar: '{{ $agrupar }}',
                periodos: {{ Js::from($periodos->values()) }},
                get labels() { return this.periodos.map(p => p.label) },
                get cotizaciones() { return this.periodos.map(p => p.cotizaciones) },
                get citas() { return this.periodos.map(p => p.citas) },
                get maxVal() {
                    const all = [...this.cotizaciones, ...this.citas];
                    return Math.max(...all, 1);
                },
                barWidth() {
                    return Math.max(4, Math.floor(560 / Math.max(this.periodos.length, 1)) - 4);
                }
            }"
        >
            <p class="mb-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Actividad por {{ $agrupar === 'dia' ? 'día' : 'semana' }}
            </p>

            {{-- Leyenda --}}
            <div class="mb-4 flex gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    Cotizaciones
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Citas
                </span>
            </div>

            {{-- Gráfica de barras SVG --}}
            <div class="w-full overflow-x-auto">
                <svg
                    x-ref="chart"
                    class="w-full"
                    style="min-width: 320px; height: 220px;"
                    viewBox="0 0 700 220"
                    preserveAspectRatio="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    {{-- Líneas de guía --}}
                    <template x-for="i in [0, 1, 2, 3, 4]">
                        <line
                            :x1="40"
                            :y1="20 + i * 40"
                            :x2="680"
                            :y2="20 + i * 40"
                            stroke="currentColor"
                            stroke-width="0.5"
                            class="text-zinc-200 dark:text-zinc-700"
                        />
                    </template>

                    {{-- Etiquetas eje Y --}}
                    <template x-for="i in [0, 1, 2, 3, 4]">
                        <text
                            :x="36"
                            :y="24 + i * 40"
                            text-anchor="end"
                            font-size="9"
                            fill="currentColor"
                            class="text-zinc-400"
                            x-text="Math.round(maxVal - (maxVal / 4) * i)"
                        ></text>
                    </template>

                    {{-- Barras --}}
                    <template x-for="(p, idx) in periodos">
                        <g>
                            {{-- Barra cotizaciones (azul) --}}
                            <rect
                                :x="44 + idx * (640 / periodos.length)"
                                :y="180 - (p.cotizaciones / maxVal) * 160"
                                :width="barWidth() / 2"
                                :height="(p.cotizaciones / maxVal) * 160"
                                rx="2"
                                fill="#3b82f6"
                                opacity="0.85"
                            />
                            {{-- Barra citas (verde) --}}
                            <rect
                                :x="44 + idx * (640 / periodos.length) + barWidth() / 2 + 2"
                                :y="180 - (p.citas / maxVal) * 160"
                                :width="barWidth() / 2"
                                :height="(p.citas / maxVal) * 160"
                                rx="2"
                                fill="#10b981"
                                opacity="0.85"
                            />
                            {{-- Etiqueta eje X --}}
                            <text
                                :x="44 + idx * (640 / periodos.length) + barWidth() / 2"
                                y="198"
                                text-anchor="middle"
                                font-size="8"
                                fill="currentColor"
                                class="text-zinc-400"
                                x-text="p.label"
                            ></text>
                        </g>
                    </template>
                </svg>
            </div>
        </div>

        {{-- Top centros e instituciones --}}
        <div class="grid gap-4 lg:grid-cols-2">

            {{-- Top centros --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="mb-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">Top centros de agenda</p>
                @forelse($topCentros as $centro)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $centro->nombre }}</span>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $centro->citas_count }} cita{{ $centro->citas_count !== 1 ? 's' : '' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Sin datos en el período</p>
                @endforelse
            </div>

            {{-- Top instituciones --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="mb-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">Top instituciones</p>
                @forelse($topInstituciones as $hospital)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $hospital->nombre_corto ?? $hospital->nombre }}</span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ $hospital->cotizaciones_count }} cot.
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Sin datos en el período</p>
                @endforelse
            </div>

        </div>
    </div>
</x-layouts::app>