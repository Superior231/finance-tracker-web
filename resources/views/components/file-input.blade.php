@props(['disabled' => false])

<input type="file" @disabled($disabled)
    {{ $attributes->merge(['class' => 'block w-full text-sm text-gray-700
        border border-gray-300 rounded-xl cursor-pointer bg-white
        focus:outline-none focus:border-green-500 focus:ring-green-500 focus:ring-1
        file:mr-3 file:py-3 file:px-3
        file:border-0 file:rounded-l-xl
        file:text-xs file:font-medium
        file:bg-green-50 file:text-green-700
        hover:file:bg-green-100
        disabled:opacity-50 disabled:cursor-not-allowed
    ']) }}>
