#!/bin/bash

echo "🔍 Diagnóstico del Sistema de Correos"
echo "===================================="
echo ""

echo "📧 1. Verificando configuración de correo..."
php artisan config:show mail.default mail.mailers.smtp mail.from
echo ""

echo "📋 2. Verificando configuración de colas..."
php artisan config:show queue.default queue.connections.database
echo ""

echo "🔄 3. Verificando trabajos en cola..."
php artisan queue:monitor
echo ""

echo "📝 4. Revisando logs recientes..."
echo "Últimos 20 logs relacionados con correos:"
tail -n 100 storage/logs/laravel.log | grep -i "mail\|SendProviderWelcomeEmail\|ProviderWelcomeMail" | tail -20
echo ""

echo "🚀 5. Verificando estado de Supervisor (si está instalado)..."
if command -v supervisorctl &> /dev/null; then
    supervisorctl status
else
    echo "Supervisor no encontrado en el sistema"
fi
echo ""

echo "💡 Para solucionar problemas de correo en producción:"
echo "1. php artisan config:clear && php artisan config:cache"
echo "2. php artisan queue:restart"
echo "3. Verificar que el worker esté ejecutándose: php artisan queue:work"
echo "4. Revisar variables MAIL_* en el .env"
