<?php

use Illuminate\Support\Facades\Route;

/**
 * Define a route for the root URL ('/').
 * When accessed, this route returns the 'welcome' view.
 *
 * Note: To redirect to '/admin', update the route logic accordingly.
 */
Route::get('/', function () {
    return redirect('/admin');
});

// Página de índice para previews de templates de correo
Route::get('/email-previews', function () {
    $users = \App\Models\User::take(3)->get();
    
    return view('email-previews', [
        'users' => $users,
    ]);
})->name('email.previews');

// Ruta para probar el template de bienvenida de proveedores
Route::get('/test-provider-welcome', function () {
    // Datos de prueba
    $user = new \App\Models\User([
        'name' => 'Juan Carlos Pérez',
        'email' => 'juan.perez@empresa.com',
    ]);
    $user->setRelation('providerProfile', new \App\Models\ProviderProfile([
        'rfc' => 'PECJ850101ABC',
        'business_name' => 'Servicios Integrales Pérez',
        'provider_type_id' => 1,
        'providerType' => (object)['name' => 'Servicios Generales'],
    ]));
    $user->setRelation('branches', collect([
        (object)['name' => 'Sucursal Centro'],
        (object)['name' => 'Sucursal Norte'],
    ]));

    return view('emails.provider-welcome', [
        'user' => $user,
        'providerProfile' => $user->providerProfile,
        'branches' => $user->branches,
    ]);
})->name('test.provider.welcome');

// Ruta para probar el envío de correo real
Route::get('/test-send-welcome/{user}', function (App\Models\User $user) {
    if (!$user->hasRole('Provider')) {
        return 'El usuario no tiene el rol Provider';
    }
    
    App\Jobs\SendProviderWelcomeEmail::dispatch($user);
    
    return 'Correo de bienvenida enviado a la cola para: ' . $user->email;
})->name('test.send.welcome');

// Ruta para probar el template de recuperación de contraseña
Route::get('/test-password-reset', function () {
    // Datos de prueba
    $user = new \App\Models\User([
        'name' => 'María Elena González',
        'email' => 'maria.gonzalez@proveedor.com',
    ]);

    // URL de prueba para el reset
    $url = route('filament.admin.auth.password-reset.reset', [
        'token' => 'test-token-123456789abcdefghijklmnopqrstuvwxyz',
        'email' => 'maria.gonzalez@proveedor.com',
    ]);

    return view('emails.password-reset', [
        'user' => $user,
        'url' => $url,
        'token' => 'test-token-123456789abcdefghijklmnopqrstuvwxyz',
    ]);
})->name('test.password.reset');

// Ruta para probar el template de recuperación de contraseña con usuario real
Route::get('/test-password-reset/{user}', function (App\Models\User $user) {
    // URL de prueba para el reset
    $url = route('filament.admin.auth.password-reset.reset', [
        'token' => 'test-token-' . bin2hex(random_bytes(16)),
        'email' => $user->email,
    ]);

    return view('emails.password-reset', [
        'user' => $user,
        'url' => $url,
        'token' => 'test-token-' . bin2hex(random_bytes(16)),
    ]);
})->name('test.password.reset.user');
