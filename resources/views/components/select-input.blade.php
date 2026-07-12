@props(['disabled' => false])

<select @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl py-2 px-3']) }}>
    {{ $slot }}
</select>
