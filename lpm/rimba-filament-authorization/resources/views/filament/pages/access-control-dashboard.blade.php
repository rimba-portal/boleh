<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <x-filament::section>
            <x-slot name="heading">Job Roles</x-slot>
            <p class="text-sm text-gray-500">Manage RIMBA job roles and their permissions.</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Permissions</x-slot>
            <p class="text-sm text-gray-500">Manage authorization permissions.</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Permission Groups</x-slot>
            <p class="text-sm text-gray-500">Organize related permissions.</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Permission Sets</x-slot>
            <p class="text-sm text-gray-500">Create reusable permission collections.</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>