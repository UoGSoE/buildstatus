<x-button
    type="button"
    {{ $attributes->merge(['class' => 'bg-white text-gray-900 ring-gray-300 hover:bg-gray-50 leading-6']) }}
>
    {{ $slot }}
</x-button>
