<x-filament::widget>
    <x-slot name="header">
        Documentos por Vencer
    </x-slot>
    <div class="space-y-4">
        @if ($expiringDocuments->isEmpty())
            <x-filament::card class="text-center text-gray-500">
                <x-slot name="header">
                    <x-filament::icon name="heroicon-o-check-circle" color="success" class="w-6 h-6 mx-auto mb-2" />
                </x-slot>
                No tienes documentos próximos a vencer.
            </x-filament::card>
        @else
            <x-filament::card>
                <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($expiringDocuments as $doc)
                        <li class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-2">
                                <x-filament::icon name="heroicon-o-document-text" color="gray" class="w-5 h-5" />
                                <span class="font-medium">{{ $doc->documentType->name }}</span>
                            </div>
                            <span class="text-xs text-danger-600 font-semibold">Vence:
                                {{ \Carbon\Carbon::parse($doc->expires_at)->format('d/m/Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-filament::card>
        @endif
    </div>
</x-filament::widget>
