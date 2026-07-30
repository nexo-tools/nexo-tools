@props(['type' => 'submit'])

{{-- The one primary CTA in the product. Shape and focus ring live in
     .nexo-btn (nexo-ui.css), shared with every other Nexo tool.

     Forms guard double submits with x-data="{ sending: false }" plus
     @submit="$nextTick(() => sending = true)" and ::disabled/::aria-busy here.
     The $nextTick matters: disabling the submit button synchronously inside the
     submit handler aborts the navigation in some browsers. --}}
<button type="{{ $type }}" {{ $attributes->class(['nexo-btn nexo-btn--primary w-full']) }}>
    {{ $slot }}
</button>
