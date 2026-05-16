<div>
    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('medicos.index') }}" wire:navigate
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $modoEdicion ? 'Editar médico' : 'Nuevo médico' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ $modoEdicion ? 'Actualiza los datos del médico' : 'Registra un nuevo médico' }}
            </p>
        </div>
    </div>

    <form wire:submit="guardar" class="flex flex-col gap-6 max-w-2xl">

        {{-- Datos personales --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold text-gray-700 uppercase tracking-wide">Datos personales</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                    <select wire:model="prefijo"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Sin prefijo</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Dra.">Dra.</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="nombre" type="text" placeholder="Nombre(s)"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Apellido</label>
                    <input wire:model="apellido" type="text" placeholder="Apellidos"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input wire:model="email" type="email" placeholder="correo@ejemplo.com"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                    <input wire:model="telefono" type="text" placeholder="55 0000 0000"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>
        </div>

        {{-- Datos profesionales --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold text-gray-700 uppercase tracking-wide">Datos profesionales</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Especialidad</label>
                    <input wire:model="especialidad" type="text" placeholder="Ej. Oncología médica"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Cédula profesional</label>
                    <input wire:model="cedula_profesional" type="text" placeholder="Número de cédula"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Institución</label>
                <select wire:model="hospital_id"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Sin institución</option>
                    @foreach ($hospitales as $hospital)
                        <option value="{{ $hospital->id }}">{{ $hospital->nombre }}</option>
                    @endforeach
                </select>
                @error('hospital_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                <textarea wire:model="notas" rows="3" placeholder="Observaciones adicionales..."
                          class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <input wire:model="activo" type="checkbox" id="activo"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label for="activo" class="text-sm font-medium text-gray-700">Médico activo</label>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('medicos.index') }}" wire:navigate
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90"
                    style="background-color:#002745;">
                {{ $modoEdicion ? 'Guardar cambios' : 'Registrar médico' }}
            </button>
        </div>

    </form>
</div>