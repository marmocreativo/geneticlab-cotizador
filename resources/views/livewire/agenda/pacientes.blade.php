<div class="p-6 space-y-4">
    <div class="flex items-center justify-between gap-2">
        <flux:heading size="lg">Pacientes</flux:heading>
        <div class="flex gap-2">
            <flux:input wire:model.live.debounce.300ms="busqueda"
                placeholder="Buscar..." icon="magnifying-glass" class="w-64" />
            <flux:button wire:click="abrirModal()" icon="plus" variant="primary">
                Nuevo paciente
            </flux:button>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Folio</th>
                    <th class="px-4 py-3 font-semibold">Paciente</th>
                    <th class="px-4 py-3 font-semibold">Contacto</th>
                    <th class="px-4 py-3 font-semibold">Citas</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($pacientes as $p)
                    <tr class="bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <td class="px-4 py-3 font-mono text-xs">{{ $p->folio }}</td>
                        <td class="px-4 py-3">
                            @if($p->anonimo)
                                <flux:badge color="zinc">Anónimo</flux:badge>
                            @else
                                <div class="font-medium">{{ $p->nombre_display }}</div>
                                @if($p->fecha_nacimiento)
                                    <div class="text-xs text-zinc-400">{{ $p->fecha_nacimiento->age }} años</div>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500 space-y-0.5">
                            @if($p->whatsapp)
                                <div>📱 {{ $p->whatsapp }}</div>
                            @endif
                            @if($p->correo)
                                <div>✉️ {{ $p->correo }}</div>
                            @endif
                            @if(!$p->whatsapp && !$p->correo)
                                <span class="text-zinc-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $p->citas->count() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1 justify-end">
                                <flux:button wire:click="abrirModal({{ $p->id }})"
                                    size="sm" variant="ghost" icon="pencil" />
                                <flux:button wire:click="eliminar({{ $p->id }})"
                                    size="sm" variant="ghost" icon="trash"
                                    wire:confirm="¿Eliminar paciente {{ $p->folio }}?" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">
                            Sin pacientes registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $pacientes->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="modalPaciente" class="w-full max-w-xl">
        <flux:heading>{{ $paciente_id ? 'Editar paciente' : 'Nuevo paciente' }}</flux:heading>

        <div class="mt-4 space-y-4">
            <flux:field variant="inline">
                <flux:label>Paciente anónimo</flux:label>
                <flux:switch wire:model.live="anonimo" />
                <flux:description>Se generará solo el folio, sin datos personales</flux:description>
            </flux:field>

            @if(!$anonimo)
                <div class="grid grid-cols-3 gap-3">
                    <flux:field>
                        <flux:label>Iniciales</flux:label>
                        <flux:input wire:model="iniciales" maxlength="10" placeholder="Ej. J.G.M." />
                    </flux:field>
                    <flux:field class="col-span-2">
                        <flux:label>Nombre</flux:label>
                        <flux:input wire:model="nombre" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>Apellido paterno</flux:label>
                        <flux:input wire:model="apellido_paterno" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Apellido materno</flux:label>
                        <flux:input wire:model="apellido_materno" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>Fecha de nacimiento</flux:label>
                        <flux:input type="date" wire:model="fecha_nacimiento" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Sexo</flux:label>
                        <flux:select wire:model="sexo" placeholder="—">
                            <flux:select.option value="M">Masculino</flux:select.option>
                            <flux:select.option value="F">Femenino</flux:select.option>
                            <flux:select.option value="otro">Otro</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>WhatsApp</flux:label>
                        <flux:input wire:model="whatsapp" type="tel" placeholder="10 dígitos" />
                        <flux:error name="whatsapp" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Correo</flux:label>
                        <flux:input wire:model="correo" type="email" />
                        <flux:error name="correo" />
                    </flux:field>
                </div>

                <p class="text-xs text-amber-600">Al menos WhatsApp o correo es requerido.</p>
            @endif

            <flux:field>
                <flux:label>Notas</flux:label>
                <flux:textarea wire:model="notas" rows="2" />
            </flux:field>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <flux:button wire:click="$set('modalPaciente', false)" variant="ghost">Cancelar</flux:button>
            <flux:button wire:click="guardar" variant="primary">Guardar</flux:button>
        </div>
    </flux:modal>
</div>