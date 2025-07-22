# Sistema de Diagnóstico de Correo

Este documento explica cómo usar el sistema de diagnóstico de correo para identificar y resolver problemas de envío de emails en producción.

## 🎯 Funcionalidades Disponibles

### 1. Interfaz Web (Filament Admin)

-   **Ubicación**: `/admin/mail-tests`
-   **Funciones**:
    -   Envío de pruebas SMTP rápidas
    -   Pruebas de correos de bienvenida para proveedores
    -   Visualización de eventos detallados
    -   Reintento de pruebas fallidas
    -   Monitoreo en tiempo real

### 2. Comandos de Consola

#### Verificar Configuración

```bash
php artisan mail:check-config
```

**Uso**: Muestra la configuración actual de correo, variables de entorno, estado de colas y supervisor.

#### Enviar Pruebas

```bash
# Prueba SMTP básica
php artisan mail:test smtp correo@ejemplo.com

# Prueba de correo de bienvenida
php artisan mail:test provider-welcome correo@ejemplo.com --user-id=1
```

#### Verificar Estado

```bash
# Ver prueba específica
php artisan mail:status 1

# Ver últimas 10 pruebas
php artisan mail:status --recent=10

# Ver solo pruebas fallidas
php artisan mail:status --failed
```

## 🔧 Solución de Problemas Comunes

### Problema 1: Correos no se envían en producción

**Síntomas**: Los correos se marcan como enviados pero no llegan al destinatario.

**Diagnóstico**:

1. Ejecutar: `php artisan mail:check-config`
2. Verificar configuración SMTP
3. Comprobar estado de supervisor/queue workers

**Soluciones**:

```bash
# Limpiar caché de configuración
php artisan config:clear
php artisan config:cache

# Reiniciar supervisor
sudo supervisorctl restart all

# Verificar procesos de cola
ps aux | grep queue:work
```

### Problema 2: Variables de entorno no se cargan

**Síntomas**: Variables muestran "No configurado" en check-config.

**Soluciones**:

1. Verificar archivo `.env` en el servidor
2. Reiniciar servidor web (nginx/apache)
3. Limpiar caché: `php artisan config:clear`

### Problema 3: Colas no procesan trabajos

**Síntomas**: Estado "pending" permanente en pruebas.

**Soluciones**:

```bash
# Procesar manualmente
php artisan queue:work --once

# Verificar configuración de supervisor
sudo supervisorctl status

# Revisar logs
tail -f storage/logs/laravel.log
```

## 📊 Interpretación de Eventos

### Estados de Prueba

-   **pending**: Prueba creada, esperando procesamiento
-   **processing**: Enviando correo
-   **sent**: Correo enviado exitosamente
-   **failed**: Error en el envío

### Tipos de Eventos

-   **info**: Información general del proceso
-   **success**: Operación exitosa
-   **warning**: Advertencia (no crítica)
-   **error**: Error que impide el envío

### Eventos Comunes

```
[21:47:35] info: Iniciando envío de correo SMTP de prueba
[21:47:36] info: Configurando destinatario: test@ejemplo.com
[21:47:37] success: Correo enviado exitosamente
```

## 🚀 Uso en Producción

### Configuración Recomendada

1. **Supervisor** (para procesar colas automáticamente):

```ini
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /ruta/del/proyecto/artisan queue:work --sleep=3 --tries=3
directory=/ruta/del/proyecto
autostart=true
autorestart=true
user=www-data
numprocs=2
```

2. **Variables de Entorno** (.env):

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="Tu Aplicación"

QUEUE_CONNECTION=database
```

### Monitoreo Continuo

1. **Prueba diaria automatizada** (cron):

```bash
# Añadir a crontab
0 9 * * * cd /ruta/proyecto && php artisan mail:test smtp admin@tudominio.com
```

2. **Verificación semanal de configuración**:

```bash
# Revisar configuración cada lunes
0 8 * * 1 cd /ruta/proyecto && php artisan mail:check-config >> /var/log/mail-check.log
```

## 🔍 Casos de Uso Específicos

### Diagnosticar Servidor de Producción

```bash
# 1. Verificar configuración completa
php artisan mail:check-config

# 2. Enviar prueba rápida
php artisan mail:test smtp admin@tudominio.com

# 3. Procesar cola manualmente (si supervisor no funciona)
php artisan queue:work --once

# 4. Verificar resultado
php artisan mail:status --recent=1
```

### Probar Después de Cambios de Configuración

```bash
# 1. Limpiar caché
php artisan config:clear

# 2. Regenerar caché
php artisan config:cache

# 3. Enviar prueba
php artisan mail:test smtp test@ejemplo.com

# 4. Verificar en interfaz web: /admin/mail-tests
```

### Depurar Errores Específicos

1. Acceder a `/admin/mail-tests`
2. Hacer clic en "Prueba SMTP Rápida"
3. Revisar la columna "Eventos" para detalles
4. Usar botón "Reintentar" si es necesario

## 📝 Logs y Registros

-   **Pruebas de correo**: Base de datos tabla `mail_tests`
-   **Logs Laravel**: `storage/logs/laravel.log`
-   **Logs del servidor**: `/var/log/nginx/` o `/var/log/apache2/`
-   **Logs de supervisor**: `/var/log/supervisor/`

## 🎯 Tips de Rendimiento

1. **No ejecutar demasiadas pruebas** simultáneamente
2. **Limpiar pruebas antiguas** periódicamente
3. **Usar `--once`** para pruebas manuales de cola
4. **Monitorear recursos** del servidor durante pruebas

## 📞 Soporte

Si los problemas persisten después de seguir esta guía:

1. Revisar logs detallados en `/admin/mail-tests`
2. Ejecutar `php artisan mail:check-config` y compartir salida
3. Verificar configuración del servidor (supervisor, nginx)
4. Contactar al administrador del sistema con los logs específicos

---

_Sistema de Diagnóstico de Correo v1.0 - Grupo Costeño_
