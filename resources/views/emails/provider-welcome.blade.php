<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a ProveedoresCosteno - Portal de Proveedores</title>
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
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* CORREGIDO: Se agregó un background-color de respaldo */
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

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white !important;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: white !important;
        }

        .content {
            padding: 2rem;
        }

        .welcome-message {
            margin-bottom: 2rem;
        }

        .welcome-message h2 {
            color: #40352b;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .welcome-message p {
            color: #57493a;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .provider-info {
            background: #f8f5f1;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #857151;
        }

        .provider-info h3 {
            color: #40352b;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #57493a;
        }

        .info-value {
            color: #6e5d48;
        }

        .cta-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        /* CORREGIDO: Se agregó un background-color de respaldo */
        .cta-button {
            display: inline-block;
            background-color: #857151 !important;
            background: linear-gradient(135deg, #857151 0%, #6e5d48 100%) !important;
            color: white !important;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(133, 113, 81, 0.25);
            border: 2px solid #857151;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(133, 113, 81, 0.3);
            background-color: #6e5d48 !important;
            background: linear-gradient(135deg, #6e5d48 0%, #57493a 100%) !important;
            color: white !important;
            border-color: #6e5d48;
        }

        .cta-button:visited,
        .cta-button:active {
            color: white !important;
        }

        .next-steps {
            background: #ece6db;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #a28a70;
        }

        .next-steps h3 {
            color: #40352b;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .steps-list {
            list-style: none;
            padding: 0;
        }

        .steps-list li {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            color: #57493a;
        }

        .steps-list li:last-child {
            margin-bottom: 0;
        }

        .step-number {
            background: #857151;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
            margin-right: 0.8rem;
            flex-shrink: 0;
        }

        .contact-info {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #22c55e;
        }

        .contact-info h3 {
            color: #15803d;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .contact-info p {
            color: #166534;
            margin-bottom: 0.5rem;
        }

        .footer {
            background: #40352b;
            color: #c5b6a3;
            padding: 2rem;
            text-align: center;
        }

        .footer p {
            margin-bottom: 0.5rem;
        }

        .footer a {
            color: #a28a70;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
            color: #b29e87;
        }

        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }

            .header,
            .content,
            .footer {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .cta-button {
                display: block;
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    @php
        $iconColor = '#857151';
        $iconSize = 18;
    @endphp
    <div class="email-container">
        <div class="header"
            style="background-color: #857151; background: linear-gradient(135deg, #857151 0%, #6e5d48 100%) !important; padding: 2rem; text-align: center;">
            <img src="{{ url('images/costeno_logo.svg') }}" alt="Costeño Logo" class="logo">
            <h1 style="color: white !important; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">¡Bienvenido!
            </h1>
            <p style="font-size: 1.1rem; opacity: 0.9; color: white !important;">Tu acceso al Portal de Proveedores está
                listo</p>
        </div>

        <div class="content">
            <div class="welcome-message">
                <h2>¡Hola, {{ $user->name }}!</h2>
                <p>Nos complace darte la bienvenida a <strong>Proveedores de Costeno</strong>, nuestro portal
                    especializado para proveedores.</p>
                <p>Tu cuenta ha sido creada exitosamente y ya puedes acceder a todas las herramientas y funcionalidades
                    que hemos preparado para ti.</p>
            </div>

            <div class="provider-info">
                <h3 style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <span style="display:inline-block;vertical-align:middle;">
                        <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="14" height="14" rx="2" stroke="{{ $iconColor }}"
                                stroke-width="1.5" />
                            <path d="M7 7H13" stroke="{{ $iconColor }}" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M7 10H13" stroke="{{ $iconColor }}" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M7 13H11" stroke="{{ $iconColor }}" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </span>
                    Información de tu Cuenta
                </h3>
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Correo electrónico:</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                @if ($providerProfile)
                    <div class="info-item">
                        <span class="info-label">RFC:</span>
                        <span class="info-value">{{ $providerProfile->rfc ?? 'No especificado' }}</span>
                    </div>
                    @if ($providerProfile->providerType)
                        <div class="info-item">
                            <span class="info-label">Tipo de Proveedor:</span>
                            <span class="info-value">{{ $providerProfile->providerType->name }}</span>
                        </div>
                    @endif
                @endif
                @if ($branches && $branches->count() > 0)
                    <div class="info-item">
                        <span class="info-label">Sucursales asignadas:</span>
                        <span class="info-value">{{ $branches->pluck('name')->implode(', ') }}</span>
                    </div>
                @endif
            </div>

            <div class="cta-section">
                <a href="{{ url('/admin') }}" class="cta-button"
                    style="display: inline-flex !important; align-items: center; justify-content: center; gap: 0.5rem; background-color: #857151; background: linear-gradient(135deg, #857151 0%, #6e5d48 100%) !important; color: white !important; text-decoration: none !important; border: 2px solid #857151 !important; padding: 1rem 2rem; border-radius: 6px; font-weight: 600; font-size: 1.1rem;">
                    <span style="display:inline-block;vertical-align:middle;">
                        <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 20 20"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 3V17" stroke="white" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M5 8L10 3L15 8" stroke="white" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span style="color: white !important;">Acceder al Portal</span>
                </a>
            </div>

            <div class="next-steps">
                <h3 style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <span style="display:inline-block;vertical-align:middle;">
                        <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 20 20"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="14" height="14" rx="2" stroke="{{ $iconColor }}"
                                stroke-width="1.5" />
                            <path d="M6 7H14" stroke="{{ $iconColor }}" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M6 10H14" stroke="{{ $iconColor }}" stroke-width="1.5"
                                stroke-linecap="round" />
                            <path d="M6 13H11" stroke="{{ $iconColor }}" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </span>
                    Próximos Pasos
                </h3>
                <ul class="steps-list">
                    <li>
                        <span class="step-number">1</span>
                        <span>Haz click en el botón superior "Acceder al portal"</span>
                    </li>
                    <li>
                        <span class="step-number">2</span>
                        <span>Despues, haz click en "Ha olvidado su contraseña" para que definas una nueva
                            contraseña</span>
                    </li>
                    <li>
                        <span class="step-number">3</span>
                        <span>Sube los documentos requeridos según tu tipo de proveedor</span>
                    </li>
                    <li>
                        <span class="step-number">4</span>
                        <span>Mantén tus documentos actualizados para evitar vencimientos</span>
                    </li>
                    <li>
                        <span class="step-number">5</span>
                        <span>Te recordamos que para continuar con tu registro es indispensable subir todos los
                            documentos a la plataforma.
                        </span>
                    </li>
                </ul>
            </div>

            <div class="contact-info">
                <h3 style="display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                    <span style="display:inline-block;vertical-align:middle;">
                        <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 20 20"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="8" stroke="{{ $iconColor }}"
                                stroke-width="1.5" />
                            <path d="M10 6V10L13 12" stroke="{{ $iconColor }}" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </span>
                    ¿Necesitas Ayuda?
                </h3>
                <p><strong>Soporte técnico:</strong> proveedores@grupocosteno.com</p>
                <p><strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 ProveedoresCosteno. Todos los derechos reservados.</p>
            <p>
                <a href="#">Términos de Servicio</a> |
                <a href="#">Política de Privacidad</a> |
                <a href="#">Contacto</a>
            </p>
        </div>
    </div>
</body>

</html>
