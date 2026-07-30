@props(['active' => false])

<a {{ $attributes->class([
    'rounded-lg px-3 py-2 text-sm',
    'bg-primary-subtle font-medium text-primary-subtle-fg' => $active,
    'text-muted hover:bg-surface-sunken hover:text-ink' => ! $active,
]) }} @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
