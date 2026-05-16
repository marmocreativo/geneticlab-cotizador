<div class="p-6 space-y-4">

    {{-- ── Encabezado ──────────────────────────────────── --}}
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2">
            <flux:button wire:click="navegar('anterior')" icon="chevron-left" variant="ghost" />
            <flux:heading size="lg">
                @if($vista === 'mensual')
                    {{ \Carbon\Carbon::parse($fecha_actual)->translatedFormat('F Y') }}
                @else
                    Semana del {{ $diasSemana[0]->translatedFormat('d M') }}
                    al {{ $diasSemana[6]->translatedFormat('d M Y') }}
                @endif
            </flux:heading>
            <flux:button wire:click="navegar('siguiente')" icon="chevron-right" variant="ghost" />
            <flux:button wire:click="hoy" size="sm" variant="outline">Hoy</flux:button>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <select wire:model.live="filtro_centro"
                class="text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                       bg-white dark:bg-zinc-800 px-3 py-2 text-zinc-700 dark:text-zinc-200">
                <option value="">Todos los centros</option>
                @foreach($centros as $c)
                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                @endforeach
            </select>

            <div class="flex rounded-lg border border-zinc-300 dark:border-zinc-600 overflow-hidden">
                <button wire:click="$set('vista','mensual')"
                    class="px-3 py-2 text-sm transition
                           {{ $vista === 'mensual' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                    Mes
                </button>
                <button wire:click="$set('vista','semanal')"
                    class="px-3 py-2 text-sm border-l border-zinc-300 dark:border-zinc-600 transition
                           {{ $vista === 'semanal' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                    Semana
                </button>
            </div>

            <flux:button wire:click="abrirWizard()" icon="plus" variant="primary">
                Nueva cita
            </flux:button>
        </div>
    </div>

    {{-- ── Vista Mensual ────────────────────────────────── --}}
    @if($vista === 'mensual')
        <div class="grid grid-cols-7 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $nd)
                <div class="bg-zinc-100 dark:bg-zinc-800 text-center text-xs font-semibold
                            py-2 text-zinc-500 border-b border-zinc-200 dark:border-zinc-700">{{ $nd }}</div>
            @endforeach

            @foreach($diasMes as $dia)
                @php
                    $key   = $dia->toDateString();
                    $esHoy = $dia->isToday();
                    $esMes = $dia->month === \Carbon\Carbon::parse($fecha_actual)->month;
                    $citas = $citasPorDia[$key] ?? collect();
                @endphp
                <div wire:click="abrirWizard('{{ $key }}')"
                    class="min-h-[90px] p-1 border-t border-r border-zinc-200 dark:border-zinc-700
                           cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition
                           {{ !$esMes ? 'bg-zinc-50 dark:bg-zinc-900/40' : 'bg-white dark:bg-zinc-900' }}">
                    <div class="flex justify-start mb-1">
                        <span class="text-xs font-medium w-6 h-6 flex items-center justify-center rounded-full
                            {{ $esHoy ? 'bg-blue-600 text-white' : ($esMes ? 'text-zinc-700 dark:text-zinc-300' : 'text-zinc-400') }}">
                            {{ $dia->day }}
                        </span>
                    </div>
                    <div class="space-y-0.5">
                        @foreach($citas->take(3) as $cita)
                            <div wire:click.stop="abrirDetalle({{ $cita->id }})"
                                class="text-[11px] truncate rounded px-1 py-0.5 cursor-pointer
                                    {{ match($cita->estado) {
                                        'programada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'confirmada' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                        'realizada'  => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400',
                                        'cancelada'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                        default      => 'bg-zinc-100',
                                    } }}">
                                {{ $cita->hora }} — {{ $cita->paciente->nombre_display }}
                            </div>
                        @endforeach
                        @if($citas->count() > 3)
                            <div class="text-[10px] text-zinc-400 pl-1">+{{ $citas->count() - 3 }} más</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Vista Semanal ────────────────────────────────── --}}
    @if($vista === 'semanal')
        <div class="grid grid-cols-7 rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            @foreach($diasSemana as $dia)
                @php
                    $key   = $dia->toDateString();
                    $esHoy = $dia->isToday();
                    $citas = $citasPorDia[$key] ?? collect();
                @endphp
                <div class="flex flex-col border-r border-zinc-200 dark:border-zinc-700 last:border-r-0">
                    <div class="text-center py-2 border-b border-zinc-200 dark:border-zinc-700
                        {{ $esHoy ? 'bg-blue-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                        <div class="text-xs font-semibold">{{ $dia->translatedFormat('D') }}</div>
                        <div class="text-sm font-bold">{{ $dia->day }}</div>
                    </div>
                    <div wire:click="abrirWizard('{{ $key }}')"
                        class="flex-1 min-h-[300px] p-1 space-y-1 cursor-pointer
                               bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        @foreach($citas as $cita)
                            <div wire:click.stop="abrirDetalle({{ $cita->id }})"
                                class="text-[11px] rounded px-1 py-1 cursor-pointer
                                    {{ match($cita->estado) {
                                        'programada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'confirmada' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                        'realizada'  => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400',
                                        'cancelada'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                        default      => 'bg-zinc-100',
                                    } }}">
                                <div class="font-semibold">{{ $cita->hora }}</div>
                                <div class="truncate">{{ $cita->paciente->nombre_display }}</div>
                                <div class="truncate text-[10px] opacity-70">{{ $cita->centro->nombre }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ── Dialog Detalle de Cita ──────────────────────── --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <flux:modal wire:model="modalDetalle" class="w-full max-w-md">
        @if($detalleCita)
            @php
                $p = $detalleCita->paciente;
                $c = $detalleCita->centro;

                $fechaFormato = $detalleCita->fecha->translatedFormat('l d \d\e F \d\e Y');
                $textoWa = urlencode(
                    "🗓 *Recordatorio de cita*\n\n" .
                    "📋 *Folio:* {$p->folio}\n" .
                    "👤 *Paciente:* {$p->nombre_display}\n" .
                    "🏥 *Centro:* {$c->nombre}\n" .
                    ($c->direccion ? "📍 *Dirección:* {$c->direccion}\n" : '') .
                    "📅 *Fecha:* {$fechaFormato}\n" .
                    "🕐 *Hora:* {$detalleCita->hora}" .
                    ($detalleCita->notas ? "\n📝 *Notas:* {$detalleCita->notas}" : '')
                );

                $waDestino = $p->whatsapp ? preg_replace('/\D/', '', $p->whatsapp) : '';
                $urlWa     = $waDestino
                    ? "https://wa.me/52{$waDestino}?text={$textoWa}"
                    : "https://wa.me/?text={$textoWa}";

                $asunto    = urlencode("Confirmación de cita — {$p->folio}");
                $cuerpoEmail = urlencode(
                    "Estimado/a {$p->nombre_display},\n\n" .
                    "Le confirmamos su cita con los siguientes datos:\n\n" .
                    "Folio: {$p->folio}\n" .
                    "Centro: {$c->nombre}\n" .
                    ($c->direccion ? "Dirección: {$c->direccion}\n" : '') .
                    "Fecha: {$fechaFormato}\n" .
                    "Hora: {$detalleCita->hora}\n" .
                    ($detalleCita->notas ? "Notas: {$detalleCita->notas}\n" : '') .
                    "\nPor favor preséntese puntualmente.\n\nGracias."
                );
                $urlEmail  = "mailto:{$p->correo}?subject={$asunto}&body={$cuerpoEmail}";
            @endphp

            {{-- Encabezado del dialog --}}
            <div class="flex items-start justify-between mb-4">
                <div>
                    <flux:heading>Detalle de cita</flux:heading>
                    <span class="font-mono text-xs text-zinc-400">{{ $p->folio }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-semibold
                    {{ match($detalleCita->estado) {
                        'programada' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                        'confirmada' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                        'realizada'  => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400',
                        'cancelada'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                        default      => 'bg-zinc-100',
                    } }}">
                    {{ ucfirst($detalleCita->estado) }}
                </span>
            </div>

            {{-- Info --}}
            <div class="space-y-3 text-sm">
                <div class="grid grid-cols-[6rem_1fr] gap-1">
                    <span class="text-zinc-400">Paciente</span>
                    <span class="font-medium">{{ $p->nombre_display }}</span>

                    @if(!$p->anonimo && $p->fecha_nacimiento)
                        <span class="text-zinc-400">Edad</span>
                        <span>{{ $p->fecha_nacimiento->age }} años</span>
                    @endif

                    @if($p->whatsapp)
                        <span class="text-zinc-400">WhatsApp</span>
                        <span>{{ $p->whatsapp }}</span>
                    @endif

                    @if($p->correo)
                        <span class="text-zinc-400">Correo</span>
                        <span>{{ $p->correo }}</span>
                    @endif
                </div>

                <flux:separator />

                <div class="grid grid-cols-[6rem_1fr] gap-1">
                    <span class="text-zinc-400">Centro</span>
                    <span class="font-medium">{{ $c->nombre }}</span>

                    @if($c->direccion)
                        <span class="text-zinc-400">Dirección</span>
                        <span>{{ $c->direccion }}</span>
                    @endif
                </div>

                <flux:separator />

                <div class="grid grid-cols-[6rem_1fr] gap-1">
                    <span class="text-zinc-400">Fecha</span>
                    <span>{{ $fechaFormato }}</span>

                    <span class="text-zinc-400">Hora</span>
                    <span>{{ $detalleCita->hora }}</span>
                </div>

                @if($detalleCita->notas)
                    <flux:separator />
                    <div class="grid grid-cols-[6rem_1fr] gap-1">
                        <span class="text-zinc-400">Notas</span>
                        <span>{{ $detalleCita->notas }}</span>
                    </div>
                @endif
            </div>

            {{-- Cambiar estado --}}
            <div class="mt-4 flex items-center gap-3">
                <span class="text-sm text-zinc-500 shrink-0">Estado:</span>
                <select wire:model.live="detalle_estado"
                    wire:change="guardarEstado"
                    class="flex-1 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                           bg-white dark:bg-zinc-800 px-3 py-2 text-zinc-700 dark:text-zinc-200">
                    <option value="programada">Programada</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="realizada">Realizada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>

            {{-- Acciones --}}
            <div class="mt-5 space-y-2">
                {{-- Enviar --}}
                <div class="flex gap-2">
                    <a href="{{ $urlWa }}" target="_blank"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                               bg-green-500 hover:bg-green-600 text-white transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.117 1.528 5.845L.057 23.43a.75.75 0 00.918.919l5.666-1.479A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.498-5.24-1.37l-.372-.215-3.863 1.008 1.028-3.772-.23-.387A9.952 9.952 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                        WhatsApp
                    </a>

                    @if($p->correo)
                        <a href="{{ $urlEmail }}"
                            class="flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                                   bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600
                                   text-zinc-700 dark:text-zinc-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Correo
                        </a>
                    @endif
                </div>

                <flux:separator />

                {{-- Editar / Eliminar --}}
                <div class="flex gap-2">
                    <flux:button wire:click="abrirEdicion" variant="outline" icon="pencil" class="flex-1">
                        Editar cita
                    </flux:button>
                    <flux:button wire:click="eliminarCita({{ $detalleCita->id }})"
                        variant="danger" icon="trash"
                        wire:confirm="¿Eliminar esta cita? Esta acción no se puede deshacer.">
                        Eliminar
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- ── Modal Wizard Nueva Cita / Edición ──────────── --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <flux:modal wire:model="modalWizard" class="w-full max-w-2xl">

        {{-- Indicador de pasos --}}
        <div class="flex items-center gap-0 mb-6">
            @foreach([1 => 'Paciente', 2 => 'Centro', 3 => 'Fecha y hora'] as $n => $label)
                <div class="flex items-center {{ $n < 3 ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition
                            {{ $paso >= $n ? 'bg-blue-600 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500' }}">
                            {{ $n }}
                        </div>
                        <span class="text-sm hidden sm:block
                            {{ $paso >= $n ? 'text-zinc-800 dark:text-zinc-200 font-medium' : 'text-zinc-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if($n < 3)
                        <div class="flex-1 mx-3 h-px {{ $paso > $n ? 'bg-blue-600' : 'bg-zinc-200 dark:bg-zinc-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ── Paso 1: Paciente ─────────────────────────── --}}
        @if($paso === 1)
            <flux:heading size="base" class="mb-4">
                {{ $modoEdicion ? 'Datos del paciente' : '¿Quién es el paciente?' }}
            </flux:heading>

            @if(!$modoEdicion)
                {{-- Toggle nuevo / existente solo en modo nueva cita --}}
                <div class="flex rounded-lg border border-zinc-300 dark:border-zinc-600 overflow-hidden mb-4 w-fit">
                    <button wire:click="$set('paciente_modo','nuevo')"
                        class="px-4 py-2 text-sm transition
                               {{ $paciente_modo === 'nuevo' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                        Registrar nuevo
                    </button>
                    <button wire:click="$set('paciente_modo','existente')"
                        class="px-4 py-2 text-sm border-l border-zinc-300 dark:border-zinc-600 transition
                               {{ $paciente_modo === 'existente' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                        Buscar existente
                    </button>
                </div>
            @endif

            {{-- Buscar existente (solo nueva cita) --}}
            @if(!$modoEdicion && $paciente_modo === 'existente')
                <div class="space-y-3">
                    <flux:field>
                        <flux:label>Buscar por nombre, folio, WhatsApp o correo</flux:label>
                        <flux:input wire:model.live.debounce.300ms="busqueda_paciente"
                            placeholder="Escribe al menos 2 caracteres..." icon="magnifying-glass" />
                    </flux:field>

                    @if($pacientesBusqueda->count())
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden
                                    divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($pacientesBusqueda as $pac)
                                <button wire:click="$set('paciente_id_sel', {{ $pac->id }})"
                                    class="w-full text-left px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition
                                           {{ $paciente_id_sel === $pac->id ? 'bg-blue-50 dark:bg-blue-900/20 border-l-2 border-blue-600' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-mono text-xs text-zinc-400">{{ $pac->folio }}</span>
                                            <span class="ml-2 text-sm font-medium">{{ $pac->nombre_display }}</span>
                                        </div>
                                        <div class="text-xs text-zinc-400">{{ $pac->whatsapp ?? $pac->correo ?? '' }}</div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @elseif(strlen($busqueda_paciente) >= 2)
                        <p class="text-sm text-zinc-400">Sin resultados. Puedes registrarlo como nuevo.</p>
                    @endif
                    <flux:error name="paciente_id_sel" />
                </div>
            @endif

            {{-- Formulario paciente (nuevo o edición) --}}
            @if($modoEdicion || $paciente_modo === 'nuevo')
                <div class="space-y-4">
                    <flux:field variant="inline">
                        <flux:label>Paciente anónimo</flux:label>
                        <flux:switch wire:model.live="anonimo" />
                        <flux:description>Sin datos personales, solo se genera un folio</flux:description>
                    </flux:field>

                    @if(!$anonimo)
                        <div class="grid grid-cols-3 gap-3">
                            <flux:field>
                                <flux:label>Iniciales</flux:label>
                                <flux:input wire:model="iniciales" maxlength="10" placeholder="J.G.M." />
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
                    @endif

                    {{-- Contacto siempre visible, anónimo o no --}}
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

                    <flux:field>
                        <flux:label>Notas del paciente</flux:label>
                        <flux:textarea wire:model="pac_notas" rows="2" />
                    </flux:field>
                </div>
            @endif
        @endif

        {{-- ── Paso 2: Centro ───────────────────────────── --}}
        @if($paso === 2)
            <flux:heading size="base" class="mb-4">¿En qué centro se presentará?</flux:heading>

            <div class="flex rounded-lg border border-zinc-300 dark:border-zinc-600 overflow-hidden mb-4 w-fit">
                <button wire:click="$set('centro_modo_nuevo', false)"
                    class="px-4 py-2 text-sm transition
                           {{ !$centro_modo_nuevo ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                    Seleccionar
                </button>
                <button wire:click="$set('centro_modo_nuevo', true)"
                    class="px-4 py-2 text-sm border-l border-zinc-300 dark:border-zinc-600 transition
                           {{ $centro_modo_nuevo ? 'bg-blue-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-600 hover:bg-zinc-50' }}">
                    Registrar nuevo
                </button>
            </div>

            @if(!$centro_modo_nuevo)
                <div class="grid grid-cols-3 gap-3 mb-3">
                    <flux:field>
                        <flux:label>Buscar</flux:label>
                        <flux:input wire:model.live.debounce.300ms="busqueda_centro" placeholder="Nombre..." />
                    </flux:field>
                    <flux:field>
                        <flux:label>Estado</flux:label>
                        <select wire:model.live="filtro_estado"
                            class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                                   bg-white dark:bg-zinc-800 px-3 py-2 text-zinc-700 dark:text-zinc-200">
                            <option value="">Todos</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}">{{ $estado }}</option>
                            @endforeach
                        </select>
                    </flux:field>
                    <flux:field>
                        <flux:label>Ciudad</flux:label>
                        <select wire:model.live="filtro_ciudad"
                            class="w-full text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                                   bg-white dark:bg-zinc-800 px-3 py-2 text-zinc-700 dark:text-zinc-200">
                            <option value="">Todas</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad }}">{{ $ciudad }}</option>
                            @endforeach
                        </select>
                    </flux:field>
                </div>

                <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden
                            divide-y divide-zinc-200 dark:divide-zinc-700 max-h-72 overflow-y-auto">
                    @forelse($centrosFiltrados as $c)
                        <button wire:click="$set('centro_id_sel', {{ $c->id }})"
                            class="w-full text-left px-4 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition
                                   {{ $centro_id_sel === $c->id ? 'bg-blue-50 dark:bg-blue-900/20 border-l-2 border-blue-600' : '' }}">
                            <div class="text-sm font-medium">{{ $c->nombre }}</div>
                            @if($c->direccion)
                                <div class="text-xs text-zinc-400 mt-0.5">{{ $c->direccion }}</div>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-zinc-400">Sin resultados</div>
                    @endforelse
                </div>
                <flux:error name="centro_id_sel" />
            @else
                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Nombre del centro</flux:label>
                        <flux:input wire:model="centro_nombre" />
                        <flux:error name="centro_nombre" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Dirección</flux:label>
                        <flux:textarea wire:model="centro_direccion" rows="2" />
                    </flux:field>
                </div>
            @endif
        @endif

        {{-- ── Paso 3: Fecha y hora ─────────────────────── --}}
        @if($paso === 3)
            <flux:heading size="base" class="mb-4">¿Cuándo es la cita?</flux:heading>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Fecha</flux:label>
                        <flux:input type="date" wire:model="cita_fecha" />
                        <flux:error name="cita_fecha" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Hora</flux:label>
                        <flux:input type="time" wire:model="cita_hora" />
                        <flux:error name="cita_hora" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Estado</flux:label>
                    <flux:select wire:model="cita_estado">
                        <flux:select.option value="programada">Programada</flux:select.option>
                        <flux:select.option value="confirmada">Confirmada</flux:select.option>
                        <flux:select.option value="realizada">Realizada</flux:select.option>
                        <flux:select.option value="cancelada">Cancelada</flux:select.option>
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Notas de la cita</flux:label>
                    <flux:textarea wire:model="cita_notas" rows="2" />
                </flux:field>

                {{-- Resumen --}}
                <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4 text-sm space-y-1">
                    <p class="font-semibold text-zinc-600 dark:text-zinc-300 mb-2">Resumen</p>
                    <div class="grid grid-cols-[5rem_1fr] gap-1">
                        <span class="text-zinc-400">Paciente</span>
                        <span>
                            @if(!$modoEdicion && $paciente_modo === 'existente' && $paciente_id_sel)
                                {{ \App\Models\Paciente::find($paciente_id_sel)?->nombre_display }}
                            @elseif($anonimo)
                                Anónimo
                            @else
                                {{ trim("$nombre $apellido_paterno") ?: '(sin nombre)' }}
                            @endif
                        </span>
                        <span class="text-zinc-400">Centro</span>
                        <span>
                            @if($centro_modo_nuevo)
                                {{ $centro_nombre ?: '—' }}
                            @else
                                {{ \App\Models\CentroAgenda::find($centro_id_sel)?->nombre ?? '—' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── Botones ───────────────────────────────────── --}}
        <div class="mt-6 flex justify-between">
            <flux:button
                wire:click="{{ $paso === 1 ? '$set(\'modalWizard\', false)' : 'anteriorPaso' }}"
                variant="ghost">
                {{ $paso === 1 ? 'Cancelar' : '← Anterior' }}
            </flux:button>

            @if($paso < 3)
                <flux:button wire:click="siguientePaso" variant="primary">Siguiente →</flux:button>
            @else
                <flux:button wire:click="guardarCita" variant="primary">
                    {{ $modoEdicion ? 'Guardar cambios' : 'Crear cita' }}
                </flux:button>
            @endif
        </div>
    </flux:modal>

</div>