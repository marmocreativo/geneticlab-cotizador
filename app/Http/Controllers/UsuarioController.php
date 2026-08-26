<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users',
            'password'     => ['required', 'confirmed', Password::min(8)],
            'prefijo'      => 'nullable|string|max:20',
            'apellidos'    => 'nullable|string|max:255',
            'puesto'       => 'nullable|string|max:100',
            'imagen_firma' => 'nullable|image|max:2048',
        ]);

        $rutaFirma = $request->hasFile('imagen_firma')
            ? $this->procesarImagenFirma($request->file('imagen_firma'))
            : null;

        User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'prefijo'      => $request->prefijo,
            'apellidos'    => $request->apellidos,
            'puesto'       => $request->puesto,
            'imagen_firma' => $rutaFirma,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password'     => ['nullable', 'confirmed', Password::min(8)],
            'prefijo'      => 'nullable|string|max:20',
            'apellidos'    => 'nullable|string|max:255',
            'puesto'       => 'nullable|string|max:100',
            'imagen_firma' => 'nullable|image|max:2048',
        ]);

        $rutaFirma = $usuario->imagen_firma;

        if ($request->hasFile('imagen_firma')) {
            if ($usuario->imagen_firma) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($usuario->imagen_firma);
            }
            $rutaFirma = $this->procesarImagenFirma($request->file('imagen_firma'));
        }

        $usuario->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'prefijo'      => $request->prefijo,
            'apellidos'    => $request->apellidos,
            'puesto'       => $request->puesto,
            'imagen_firma' => $rutaFirma,
            ...($request->filled('password')
                ? ['password' => Hash::make($request->password)]
                : []),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    private function procesarImagenFirma($file): string
    {
        $manager = \Intervention\Image\ImageManager::usingDriver(
            \Intervention\Image\Drivers\Gd\Driver::class
        );

        $imagen = $manager->decode($file)->scale(width: 400);

        $nombreArchivo = 'firmas/' . uniqid('firma_') . '.png';

        \Illuminate\Support\Facades\Storage::disk('public')->put(
            $nombreArchivo,
            (string) $imagen->encodeUsingFileExtension('png')
        );

        return $nombreArchivo;
    }
}