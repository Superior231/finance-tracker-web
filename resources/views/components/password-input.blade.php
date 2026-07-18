@props(['disabled' => false])

<div x-data="{ show: false }" class="relative position-relative">
    {{-- Input --}}
    <input
        :type="show ? 'text' : 'password'"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl py-2 pl-4 pr-10 block w-full']) !!}
    >

    <button
        type="button"
        @click="show = !show"
        tabindex="-1"
        class="absolute text-gray-500 position-absolute text-muted"
        style="right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 0;"
    >
        <i class="fa-regular fs-7" :class="show ? 'fa-eye' : 'fa-eye-slash'"></i>
    </button>
</div>