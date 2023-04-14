<div>
  <div {{ $attributes->merge(['class' => "mx-auto max-w-7xl px-2"]) }}>
    <div class="relative flex h-16 items-center justify-between">
      {{ $slot }}
    </div>
  </div>
</div>
