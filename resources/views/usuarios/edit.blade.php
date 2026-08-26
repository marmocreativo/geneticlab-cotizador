<x-layouts::app :title="__('Editar usuario')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('usuarios.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar usuario</h1>
            <p class="text-sm text-gray-500">{{ $usuario->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('usuarios.update', $usuario) }}" enctype="multipart/form-data" class="max-w-lg flex flex-col gap-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

                        <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                    <select name="prefijo"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">—</option>
                        @foreach (['Dr.', 'Dra.', 'Lic.', 'Ing.', 'Mtro.', 'Mtra.'] as $opcion)
                            <option value="{{ $opcion }}" @selected(old('prefijo', $usuario->prefijo) === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
                           class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('apellidos') ? 'border-red-400' : 'border-gray-200' }}" />
                    @error('apellidos')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Puesto</label>
                <input type="text" name="puesto" value="{{ old('puesto', $usuario->puesto) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('puesto') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('puesto')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ preview: null }">
                <label class="mb-1 block text-sm font-medium text-gray-700">Imagen de firma</label>

                @if ($usuario->imagen_firma_url)
                    <div class="mb-2">
                        <img src="{{ $usuario->imagen_firma_url }}" class="h-20 rounded-lg border border-gray-200 bg-gray-50 object-contain p-2" />
                        <p class="mt-1 text-xs text-gray-400">Firma actual. Sube una nueva imagen para reemplazarla.</p>
                    </div>
                @endif

                <input type="file" name="imagen_firma" accept="image/*"
                       @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                @error('imagen_firma')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <div x-show="preview" class="mt-3">
                    <img :src="preview" class="h-20 rounded-lg border border-gray-200 bg-gray-50 object-contain p-2" />
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <p class="mb-3 text-xs text-gray-400">Deja en blanco para mantener la contraseña actual.</p>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nueva contraseña</label>
                        <input type="password" name="password"
                               class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }}" />
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('usuarios.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                Guardar cambios
            </button>
        </div>
    </form>

</x-layouts::app>