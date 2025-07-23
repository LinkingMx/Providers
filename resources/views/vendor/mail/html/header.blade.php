@props(['url'])
<tr>
    <td class="header costeno-header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'ProveedoresCosteno')
                <img src="{{ asset('images/costeno_logo.svg') }}" class="costeno-logo" alt="Grupo Costeño">
            @else
                {{ $slot }}
            @endif
        </a>
        <h1>Portal de Proveedores</h1>
        <p>Grupo Costeño</p>
    </td>
</tr>'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
                <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @else
                {!! $slot !!}
            @endif
        </a>
    </td>
</tr>
