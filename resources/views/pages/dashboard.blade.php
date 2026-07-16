<x-app-layout :title="$title">
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800">
            {{ $navTitle }}
        </h2>
    </x-slot>

    <div class="py-2 mx-auto px-6 max-w-7xl lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
</x-app-layout>
