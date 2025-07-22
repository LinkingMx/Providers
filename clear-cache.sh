#!/bin/bash

# Script para limpiar cachés en producción
echo "🧹 Limpiando cachés de Laravel..."

# Limpiar caché de configuración
php artisan config:clear
echo "✅ Configuración limpiada"

# Limpiar caché de rutas
php artisan route:clear
echo "✅ Rutas limpiadas"

# Limpiar caché de vistas
php artisan view:clear
echo "✅ Vistas limpiadas"

# Limpiar caché de aplicación
php artisan cache:clear
echo "✅ Caché de aplicación limpiado"

# Limpiar archivos compilados de configuración
php artisan clear-compiled
echo "✅ Archivos compilados limpiados"

echo ""
echo "🔄 Reconstruyendo cachés para producción..."

# Cachear configuración (recomendado para producción)
php artisan config:cache
echo "✅ Configuración cacheada"

# Cachear rutas (recomendado para producción)
php artisan route:cache
echo "✅ Rutas cacheadas"

# Cachear vistas (recomendado para producción)
php artisan view:cache
echo "✅ Vistas cacheadas"

echo ""
echo "🎉 ¡Cachés actualizados! Los nuevos valores del .env ya están disponibles."
echo ""
echo "📋 Verificar configuración actual:"
echo "php artisan config:show mail"
