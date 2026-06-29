<x-filament-panels::page>
    <div class="max-w-3xl mx-auto">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button color="primary" wire:click="save" icon="heroicon-o-check">
                {{ __('filament.actions.save') }}
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
