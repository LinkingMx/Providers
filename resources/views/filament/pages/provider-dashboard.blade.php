<x-filament::page>


    <div class="flex flex-row gap-2 flex-nowrap">
        <div class="flex-1 min-w-0">
            @livewire(\App\Filament\Widgets\Provider\ProviderStatsWidget::class)
        </div>

    </div>
    <div class="mt-6">
        @livewire(\App\Filament\Widgets\Provider\ProviderDocumentManagerWidget::class)
    </div>
</x-filament::page>
