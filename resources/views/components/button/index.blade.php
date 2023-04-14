<button
    type="button"
    {{ $attributes->merge(['class' => 'rounded-md py-1.5 px-2.5 text-sm font-semibold shadow-sm ring-1 ring-inset']) }}
>
    {{ $slot }}
</button>
