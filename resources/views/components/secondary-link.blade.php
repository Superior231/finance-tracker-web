<a
    {{ $attributes->merge(['class' => 'flex justify-center items-center gap-2 px-4 py-3 bg-gray-100 border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
