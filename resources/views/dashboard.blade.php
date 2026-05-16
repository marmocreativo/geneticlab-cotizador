<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">

        {{-- Tarjetas de estadísticas --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gl-border-default bg-gl-charcoal p-5">
                <p class="text-sm text-gl-text-secondary">Cotizaciones este mes</p>
                <p class="mt-1 text-3xl font-semibold text-gl-text-primary">—</p>
                <p class="mt-1 text-xs text-gl-text-muted">Sin datos aún</p>
            </div>
            <div class="rounded-xl border border-gl-border-default bg-gl-charcoal p-5">
                <p class="text-sm text-gl-text-secondary">Médicos registrados</p>
                <p class="mt-1 text-3xl font-semibold text-gl-text-primary">—</p>
                <p class="mt-1 text-xs text-gl-text-muted">Sin datos aún</p>
            </div>
            <div class="rounded-xl border border-gl-border-default bg-gl-charcoal p-5">
                <p class="text-sm text-gl-text-secondary">Instituciones</p>
                <p class="mt-1 text-3xl font-semibold text-gl-text-primary">—</p>
                <p class="mt-1 text-xs text-gl-text-muted">Sin datos aún</p>
            </div>
            <div class="rounded-xl border border-gl-border-default bg-gl-charcoal p-5">
                <p class="text-sm text-gl-text-secondary">Importe total cotizado</p>
                <p class="mt-1 text-3xl font-semibold text-gl-cyan-400">—</p>
                <p class="mt-1 text-xs text-gl-text-muted">Sin datos aún</p>
            </div>
        </div>

        {{-- Área principal --}}
        <div class="relative flex-1 overflow-hidden rounded-xl border border-gl-border-default bg-gl-charcoal p-6">
            <p class="text-sm font-medium text-gl-text-secondary">Actividad reciente</p>
            <div class="mt-4 flex items-center justify-center h-48">
                <p class="text-gl-text-muted text-sm">Las cotizaciones recientes aparecerán aquí</p>
            </div>
        </div>

    </div>
</x-layouts::app>