<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 btn btn-primary rounded-10 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
