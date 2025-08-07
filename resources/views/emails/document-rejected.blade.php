<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento Rechazado - Portal de Proveedores</title>
    <style>
        :root {
            /* Definimos colores para fácil manejo en modo oscuro */
            --bg-light: #f8fafc;
            --container-bg-light: #ffffff;
            --text-light: #333;
            --primary-color: #857151;
            --text-secondary-light: #4a5568;
            --danger-color: #dc3545;

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

        .rejection-alert {
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
            border-left: 4px solid var(--danger-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .rejection-alert h4 {
            color: var(--danger-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }

        .rejection-alert .document-name {
            color: #a71d2a;
            font-weight: 600;
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }

        .rejection-reason-box {
            background-color: #fff5f5;
            border: 1px solid #e57373;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .rejection-reason-box h5 {
            color: var(--danger-color);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .rejection-reason-box p {
            color: #a71d2a;
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
            white-space: pre-wrap;
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

        .action-required {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-left: 4px solid #f39c12;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .action-required h4 {
            color: #f39c12;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .action-required p {
            color: #856404;
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

            .rejection-alert {
                background-color: #4d1115 !important;
                border-color: #a72730 !important;
                border-left-color: #dc3545 !important;
            }

            .rejection-alert h4,
            .rejection-alert .document-name {
                color: #ff8a95 !important;
            }

            .rejection-reason-box {
                background-color: #3d1115 !important;
                border-color: #a72730 !important;
            }

            .rejection-reason-box h5 {
                color: #ff8a95 !important;
            }

            .rejection-reason-box p {
                color: #ffb3ba !important;
            }

            .action-required {
                background-color: #4d3c11 !important;
                border-color: #a47e27 !important;
                border-left-color: #f39c12 !important;
            }

            .action-required h4,
            .action-required p,
            .action-required svg {
                color: #fce18d !important;
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
            <h2 class="greeting">¡Hola {{ $providerName }}!</h2>

            <p class="intro">
                Recibiste este correo porque uno de tus documentos ha sido <strong>rechazado</strong> y requiere tu atención inmediata.
            </p>

            <div class="rejection-alert">
                <h4>
                    <svg class="icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Documento Rechazado
                </h4>
                <p class="document-name">📄 {{ $documentName }}</p>
                
                <div class="rejection-reason-box">
                    <h5>Motivo del Rechazo:</h5>
                    <p>{{ $rejectionReason }}</p>
                </div>
            </div>

            <div class="action-required">
                <h4>
                    <svg class="icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Acción Requerida
                </h4>
                <p>
                    Te solicitamos ingresar a tu cuenta del Portal de Proveedores y hacer la actualización necesaria del documento especificado.
                </p>
            </div>

            <div class="cta-section">
                <a href="{{ $portalUrl }}" class="cta-button">
                    📝 Actualizar Documento
                </a>
            </div>

            <div class="info-section">
                <h3>¿Qué hacer ahora?</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>1. Revisa el motivo</strong>
                        <span>Lee cuidadosamente la razón del rechazo</span>
                    </div>
                    <div class="info-item">
                        <strong>2. Prepara el documento</strong>
                        <span>Asegúrate de cumplir con los requisitos</span>
                    </div>
                    <div class="info-item">
                        <strong>3. Sube nuevamente</strong>
                        <span>Ingresa al portal y actualiza tu documento</span>
                    </div>
                </div>
            </div>

            <p style="font-size: 0.9rem; color: #6b7280; margin-top: 2rem;">
                <strong>¿Necesitas ayuda?</strong><br>
                Si tienes dudas sobre los requisitos del documento o el proceso de actualización, no dudes en contactarnos.
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