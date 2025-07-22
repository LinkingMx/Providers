@php
    $events = $getState() ?? [];
@endphp

@if (empty($events))
    <div class="text-gray-500 italic">
        No hay eventos registrados
    </div>
@else
    <div class="space-y-3">
        @foreach ($events as $index => $event)
            <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $event['event'] ?? 'Evento desconocido' }}
                        </h4>
                        <time class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($event['timestamp'])->format('H:i:s') }}
                        </time>
                    </div>
                    @if (!empty($event['data']))
                        <div class="mt-1">
                            <details class="group">
                                <summary
                                    class="text-xs text-gray-600 dark:text-gray-300 cursor-pointer hover:text-gray-800 dark:hover:text-gray-100">
                                    Ver datos
                                </summary>
                                <div
                                    class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs font-mono overflow-x-auto">
                                    <pre>{{ json_encode($event['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
