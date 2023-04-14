@props([
    'name' => '',
    'id' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'type' => 'text',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'autofocus' => false,
    'autocomplete' => 'off',
    'withButton' => false,
])

<div class="rounded-md shadow-sm">
    <div>
        @if ($label)
            <label for="{{ $id }}" class="mt-2 block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
        @endif
        <div @class([
            "mt-2" => $label,
            "flex",
            "rounded-md",
            "shadow-sm",
        ])>
          <div class="grow relative flex flex-grow items-stretch focus-within:z-10">
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => "block w-full rounded-none rounded-l-md border-0 py-1.5 pl-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-900 sm:text-sm sm:leading-6"]) }} placeholder="{{ $placeholder }}">
          </div>
          @if ($withButton)
            <button type="button" {{ $button->attributes->class(["relative -ml-px flex-initial inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"]) }}>
                {{ $button }}
            </button>
          @endif
        </div>
    </div>
</div>
