<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previews de Templates de Correo - Portal de Proveedores</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #857151;
        }

        .header h1 {
            color: #857151;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .previews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .preview-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .preview-card:hover {
            border-color: #857151;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(133, 113, 81, 0.1);
        }

        .preview-card h3 {
            color: #857151;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .preview-card p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }

        .preview-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .preview-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #857151 0%, #6e5d48 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
        }

        .preview-link:hover {
            background: linear-gradient(135deg, #6e5d48 0%, #57493a 100%);
            transform: translateY(-1px);
        }

        .preview-link.secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .preview-link.secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        }

        .users-section {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .users-section h3 {
            color: #857151;
            margin-bottom: 1rem;
        }

        .users-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .user-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #857151;
        }

        .user-item strong {
            color: #857151;
        }

        .user-item .email {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .user-links {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .user-link {
            padding: 0.25rem 0.75rem;
            background: #857151;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.8rem;
            transition: background 0.3s ease;
        }

        .user-link:hover {
            background: #6e5d48;
        }

        .info-section {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-left: 4px solid #2196f3;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .info-section h4 {
            color: #1976d2;
            margin-bottom: 0.5rem;
        }

        .info-section p {
            color: #0d47a1;
            margin: 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .previews-grid {
                grid-template-columns: 1fr;
            }

            .users-list {
                grid-template-columns: 1fr;
            }

            .user-links {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📧 Previews de Templates de Correo</h1>
            <p>Portal de Proveedores - Grupo Costeño</p>
        </div>

        <div class="previews-grid">
            <div class="preview-card">
                <h3>🎉 Correo de Bienvenida</h3>
                <p>Template que se envía automáticamente cuando se crea un nuevo proveedor o se asigna el rol Provider a
                    un usuario.</p>
                <div class="preview-links">
                    <a href="{{ route('test.provider.welcome') }}" class="preview-link" target="_blank">
                        Ver Preview con Datos de Prueba
                    </a>
                </div>
            </div>

            <div class="preview-card">
                <h3>🔐 Recuperación de Contraseña</h3>
                <p>Template que se envía cuando un usuario solicita restablecer su contraseña a través del portal.</p>
                <div class="preview-links">
                    <a href="{{ route('test.password.reset') }}" class="preview-link" target="_blank">
                        Ver Preview con Datos de Prueba
                    </a>
                </div>
            </div>
        </div>

        @if ($users->count() > 0)
            <div class="users-section">
                <h3>👥 Probar con Usuarios Reales</h3>
                <p style="margin-bottom: 1rem; color: #6b7280;">Haz clic en los enlaces para ver los templates con datos
                    reales de usuarios existentes:</p>

                <div class="users-list">
                    @foreach ($users as $user)
                        <div class="user-item">
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <div class="email">{{ $user->email }}</div>
                            </div>
                            <div class="user-links">
                                <a href="{{ route('test.password.reset.user', $user->id) }}" class="user-link"
                                    target="_blank">
                                    Reset Password
                                </a>
                                @if ($user->hasRole('Provider'))
                                    <a href="{{ route('test.send.welcome', $user->id) }}" class="user-link"
                                        target="_blank">
                                        Send Welcome
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="info-section">
            <h4>ℹ️ Información Importante</h4>
            <p>
                <strong>Estos son solo previews</strong> - Los enlaces de "Send Welcome" envían correos reales a la
                cola.
                Los previews de templates no envían correos, solo muestran cómo se verían.
                <br><br>
                <strong>Para ver en producción:</strong> Asegúrate de que el queue worker esté ejecutándose con
                <code>php artisan queue:work</code>
            </p>
        </div>
    </div>
</body>

</html>
