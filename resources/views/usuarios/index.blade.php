<x-layouts::app :title="__('Usuarios')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Usuarios</h1>
            <p class="text-sm text-gray-500">Cuentas con acceso al sistema</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Usuario</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Correo</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Registro</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">2FA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-full text-xs font-semibold text-white"
                                     style="background-color:#002745;">
                                    {{ $usuario->initials() }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $usuario->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($usuario->two_factor_confirmed_at)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-500">
                                    No configurado
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-gray-400">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>

</x-layouts::app>