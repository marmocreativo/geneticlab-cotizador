<div class="p-6 space-y-4">
    <div class="flex items-center justify-between gap-2">
        <flux:heading size="lg">Centros / Laboratorios</flux:heading>
        <div class="flex gap-2">
            <flux:input wire:model.live.debounce.300ms="busqueda"
                placeholder="Buscar..." icon="magnifying-glass" class="w-64" />
            <flux:button wire:click="abrirModal()" icon="plus" variant="primary">
                Nuevo centro
            </flux:button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Nombre</th>
                    <th class="px-4 py-3 font-semibold">Dirección</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($centros as $c)
                    <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <td class="px-4 py-3 font-medium">{{ $c->nombre }}</td>
                        <td class="px-4 py-3 text-zinc-500 max-w-sm">{{ $c->direccion ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$c->activo ? 'green' : 'zinc'">
                                {{ $c->activo ? 'Activo' : 'Inactivo' }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1 justify-end">
                                <flux:button wire:click="toggleActivo({{ $c->id }})"
                                    size="sm" variant="ghost"
                                    icon="{{ $c->activo ? 'eye-slash' : 'eye' }}" />
                                <flux:button wire:click="abrirModal({{ $c->id }})"
                                    size="sm" variant="ghost" icon="pencil" />
                                <flux:button wire:click="eliminar({{ $c->id }})"
                                    size="sm" variant="ghost" icon="trash"
                                    wire:confirm="¿Eliminar '{{ $c->nombre }}'?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-400">
                            Sin centros registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $centros->links() }}</div>

    <flux:modal wire:model="modalCentro" class="w-full max-w-lg">
        <flux:heading>{{ $centro_id ? 'Editar centro' : 'Nuevo centro' }}</flux:heading>

        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Nombre</flux:label>
                <flux:input wire:model="nombre" />
                <flux:error name="nombre" />
            </flux:field>
            <flux:field>
                <flux:label>Dirección</flux:label>
                <flux:textarea wire:model="direccion" rows="2" />
            </flux:field>
            <flux:field variant="inline">
                <flux:label>Activo</flux:label>
                <flux:switch wire:model="activo" />
            </flux:field>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <flux:button wire:click="$set('modalCentro', false)" variant="ghost">Cancelar</flux:button>
            <flux:button wire:click="guardar" variant="primary">Guardar</flux:button>
        </div>
    </flux:modal>
</div>