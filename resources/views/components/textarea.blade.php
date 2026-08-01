@props(['disabled' => false, 'rows' => 3])

<textarea @disabled($disabled) rows="{{ $rows }}"
    {{ $attributes->merge([
        'class' => 'border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl py-3 px-4 w-full',
    ]) }}>{{ $slot }}</textarea>
