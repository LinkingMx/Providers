<?php

return [

    'title' => 'Restablecer tu contraseña',

    'heading' => '¿Olvidaste tu contraseña?',

    'actions' => [

        'login' => [
            'label' => 'Volver al inicio de sesión',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Correo electrónico',
        ],

        'actions' => [

            'request' => [
                'label' => 'Enviar email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'title' => 'Correo enviado',
            'body' => 'Si tu cuenta existe, recibirás un correo con las instrucciones para restablecer tu contraseña.',
        ],

        'throttled' => [
            'title' => 'Demasiadas solicitudes',
            'body' => 'Por seguridad, debes esperar :seconds segundos antes de solicitar nuevamente el restablecimiento de contraseña.',
        ],

    ],

];
