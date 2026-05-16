<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeneticLab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color:#ffffff; color:#101828; min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1.5rem 1.5rem 3rem; font-family:'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">

    <header style="width:100%; max-width:64rem; margin-bottom:4rem; display:flex; align-items:center; justify-content:space-between;">
        <span style="color:#002745; font-weight:600; font-size:0.9375rem; letter-spacing:-0.01em;">GeneticLab</span>

        @if (Route::has('login'))
            <nav>
                @auth
                    <a href="{{ route('dashboard') }}"
                       style="display:inline-block; padding:0.375rem 1rem; border-radius:0.375rem; border:1px solid #002745; color:#002745; font-size:0.8125rem; text-decoration:none;">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       style="display:inline-block; color:#a1a1aa; font-size:0.8125rem; text-decoration:none;">
                        Iniciar sesión
                    </a>
                @endauth
            </nav>
        @endif
    </header>

    <main style="width:100%; max-width:64rem; display:flex; flex-direction:column; align-items:center; text-align:center; gap:1.25rem;">

        <span style="font-size:0.6875rem; font-weight:500; color:#a1a1aa; letter-spacing:0.12em; text-transform:uppercase;">
            Diagnóstico Molecular Oncológico
        </span>

        <h1 style="font-size:clamp(2rem, 5vw, 3rem); font-weight:700; color:#002745; line-height:1.15; max-width:36rem; margin:0;">
            Sistema de cotización
        </h1>

        <p style="color:#a1a1aa; font-size:1rem; max-width:30rem; margin:0; line-height:1.7;">
            Plataforma interna para gestión de médicos, instituciones y cotizaciones de estudios moleculares.
        </p>

        @guest
            <a href="{{ route('login') }}"
               style="margin-top:0.75rem; display:inline-block; padding:0.625rem 2rem; border-radius:0.375rem; background:#002745; color:#ffffff; font-weight:500; font-size:0.875rem; text-decoration:none;">
                Acceder al sistema
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               style="margin-top:0.75rem; display:inline-block; padding:0.625rem 2rem; border-radius:0.375rem; background:#002745; color:#ffffff; font-weight:500; font-size:0.875rem; text-decoration:none;">
                Ir al dashboard
            </a>
        @endguest

        <div style="margin-top:3rem; display:grid; grid-template-columns:repeat(3,1fr); gap:2rem; text-align:center; border-top:1px solid #f4f4f5; padding-top:2.5rem; width:100%; max-width:26rem;">
            <div>
                <p style="font-size:1.25rem; font-weight:600; color:#002745; margin:0;">+5,300</p>
                <p style="font-size:0.6875rem; color:#d4d4d8; margin-top:0.25rem;">pruebas procesadas</p>
            </div>
            <div>
                <p style="font-size:1.25rem; font-weight:600; color:#002745; margin:0;">72 hrs</p>
                <p style="font-size:0.6875rem; color:#d4d4d8; margin-top:0.25rem;">tiempo de entrega</p>
            </div>
            <div>
                <p style="font-size:1.25rem; font-weight:600; color:#002745; margin:0;">ISO 9001</p>
                <p style="font-size:0.6875rem; color:#d4d4d8; margin-top:0.25rem;">certificación</p>
            </div>
        </div>

    </main>

    <footer style="margin-top:4rem; color:#d4d4d8; font-size:0.6875rem;">
        © {{ date('Y') }} GeneticLab. Todos los derechos reservados.
    </footer>

</body>
</html>