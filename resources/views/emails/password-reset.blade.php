<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - Portal de Proveedores</title>
    <style>
        :root {
            /* Definimos colores para fácil manejo en modo oscuro */
            --bg-light: #f8fafc;
            --container-bg-light: #ffffff;
            --text-light: #333;
            --primary-color: #857151;
            --text-secondary-light: #4a5568;

            --bg-dark: #1a1a1a;
            --container-bg-dark: #2d2d2d;
            --text-dark: #e0e0e0;
            --text-secondary-dark: #b0b0b0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: var(--text-light);
            background-color: var(--bg-light);
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--container-bg-light);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* CORREGIDO: Fallback para gradiente */
        .header {
            background-color: #857151;
            background: linear-gradient(135deg, #857151 0%, #6e5d48 100%);
            color: white !important;
            padding: 2rem;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 1rem;
            filter: brightness(0) invert(1);
        }

        .header h1,
        .header p {
            color: white !important;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content {
            padding: 2rem;
            background-color: var(--container-bg-light);
        }

        .greeting {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .intro {
            font-size: 1.1rem;
            color: var(--text-secondary-light);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .security-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #f39c12;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .security-alert h4 {
            color: #f39c12;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .security-alert p {
            color: #856404;
            margin: 0;
            font-size: 14px;
        }

        .icon {
            width: 20px;
            height: 20px;
            margin-right: 0.5rem;
        }

        .cta-section {
            text-align: center;
            margin: 2rem 0;
        }

        /* CORREGIDO: Fallback para gradiente */
        .cta-button {
            display: inline-block;
            background-color: #857151;
            background: linear-gradient(135deg, #857151 0%, #6e5d48 100%);
            color: white !important;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(133, 113, 81, 0.3);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: #6e5d48;
            background: linear-gradient(135deg, #6e5d48 0%, #57493a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(133, 113, 81, 0.4);
        }

        .info-section {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .info-section h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .info-item strong,
        .footer .contact-item strong {
            color: var(--primary-color);
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-item span,
        .footer .contact-item span {
            color: var(--text-secondary-light);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 2rem;
            text-align: center;
            border-top: 3px solid var(--primary-color);
        }

        .footer h3 {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .footer p {
            color: var(--primary-color);
            font-size: 14px;
            margin-top: 1rem;
        }

        .footer .contact-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .expiry-info {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-left: 4px solid #2196f3;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .expiry-info h4 {
            color: #1976d2;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .expiry-info p {
            color: #0d47a1;
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }
        }

        /********************************************/
        /* NUEVO: ESTILOS MODO OSCURO         */
        /********************************************/
        @media (prefers-color-scheme: dark) {

            body,
            .content {
                background-color: var(--bg-dark) !important;
                color: var(--text-dark) !important;
            }

            .email-container,
            .info-section,
            .footer {
                background-color: var(--container-bg-dark) !important;
                box-shadow: none !important;
            }

            .intro,
            .info-item span,
            .footer .contact-item span {
                color: var(--text-secondary-dark) !important;
            }

            p,
            h2,
            h3,
            h4,
            strong {
                color: var(--text-dark) !important;
            }

            .greeting,
            .info-section h3,
            .info-item strong,
            .footer h3,
            .footer .contact-item strong,
            .footer p {
                color: #e8d5b1 !important;
                /* Un tono más claro del color primario */
            }

            .security-alert {
                background-color: #4d3c11 !important;
                border-color: #a47e27 !important;
                border-left-color: #f39c12 !important;
            }

            .security-alert h4,
            .security-alert p,
            .security-alert svg {
                color: #fce18d !important;
            }

            .expiry-info {
                background-color: #1c3d5a !important;
                border-color: #3b7cb6 !important;
                border-left-color: #64b5f6 !important;
            }

            .expiry-info h4,
            .expiry-info p,
            .expiry-info svg {
                color: #bbdefb !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/costeno_logo.svg') }}" alt="Grupo Costeño" class="logo">
            <h1>Portal de Proveedores</h1>
            <p>Grupo Costeño</p>
        </div>

        <div class="content">
            <h2 class="greeting">¡Hola {{ $user->name }}!</h2>

            <p class="intro">
                Recibiste este correo porque se solicitó una recuperación de contraseña para tu cuenta en el
                <strong>Portal de Proveedores de Grupo Costeño</strong>.
            </p>

            <div class="security-alert">
                <h4>
                    <svg class="icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Aviso de Seguridad
                </h4>
                <p>
                    Por tu seguridad, nunca compartas este enlace con nadie más. Si sospechas que alguien más tiene
                    acceso a tu cuenta, contáctanos inmediatamente.
                </p>
            </div>

            <div class="expiry-info">
                <h4>
                    <svg class="icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Tiempo Límite
                </h4>
                <p>
                    Este enlace de recuperación expirará en
                    <strong>{{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60) }}
                        minutos</strong>.
                    Asegúrate de usarlo pronto.
                </p>
            </div>

            <div class="cta-section">
                <a href="{{ $url }}" class="cta-button">
                    🔐 Restablecer Mi Contraseña
                </a>
            </div>

            <div class="info-section">
                <h3>¿Qué pasa después?</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>1. Haz clic en el botón</strong>
                        <span>Serás redirigido a una página segura</span>
                    </div>
                    <div class="info-item">
                        <strong>2. Crea nueva contraseña</strong>
                        <span>Elige una contraseña segura y única</span>
                    </div>
                    <div class="info-item">
                        <strong>3. Inicia sesión</strong>
                        <span>Accede con tu nueva contraseña</span>
                    </div>
                </div>
            </div>

            <p style="font-size: 0.9rem; color: #6b7280; margin-top: 2rem;">
                <strong>¿No solicitaste una recuperación de contraseña?</strong><br>
                Si no fuiste tú quien solicitó este cambio, puedes ignorar este correo de forma segura.
                Tu contraseña actual seguirá siendo válida.
            </p>
        </div>

        <div class="footer">
            <h3>Soporte y Contacto</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <strong>Email</strong>
                    <span>proveedores@grupocosteno.com</span>
                </div>
                <div class="contact-item">
                    <strong>Horario de Atención</strong>
                    <span>Lunes a Viernes 8:00 - 18:00</span>
                </div>
            </div>
            <p>
                © {{ date('Y') }} Grupo Costeño. Todos los derechos reservados.<br>
                Este es un correo automático, por favor no responder directamente.
            </p>
        </div>
    </div>
</body>

</html>
