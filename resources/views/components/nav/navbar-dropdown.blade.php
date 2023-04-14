<div class="relative inline-block text-left" x-data="{ open: false }"  x-on:click.outside="open = false">
  <div>
    <button x-on:click="open = ! open" x-bind:class="open ? 'bg-sky-500 text-white' : 'text-gray-200'" type="button" class="text-gray-200 inline-flex w-full justify-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold shadow-sm hover:bg-sky-500 hover:text-white" {{ $attributes }} x-bind:aria-expanded="open" aria-haspopup="true">
      {{ $trigger }}
      <svg class="-mr-1 h-5 w-5 text-gray-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
      </svg>
    </button>
  </div>

  <div x-cloak x-show="open" x-transition class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
    <div class="py-1" role="none">
        {{ $slot }}
    </div>
  </div>
</div>
