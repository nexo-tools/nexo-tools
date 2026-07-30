<x-app-layout>
    <header class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Tus herramientas') }}</h1>
        <p class="mt-1 text-muted">{{ __('Lanza cualquier herramienta Nexo desde aquí.') }}</p>
    </header>

    @if (count($added) === 0)
        {{-- Empty state: invite to explore the registry. (AC-TOOLS-4) --}}
        <section class="rounded-2xl border border-line bg-surface-raised p-8 text-center">
            <p class="text-lg font-semibold">{{ __('Todavía no añadiste herramientas') }}</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-muted">{{ __('Explora el ecosistema Nexo y añade las que uses para tenerlas siempre a mano.') }}</p>
            @if (count($available) > 0)
                <a href="#anadir" class="nexo-btn nexo-btn--primary mt-5">{{ __('Añadir herramientas') }}</a>
            @endif
        </section>
    @else
        {{-- Launch cards for the tools the user added. (AC-TOOLS-1) --}}
        <section class="grid gap-4 sm:grid-cols-2">
            @foreach ($added as $tool)
                <div class="flex flex-col rounded-2xl border border-line bg-surface-raised p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <img src="{{ $tool['mark'] }}" alt="" width="40" height="40" class="rounded-xl">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold">{{ $tool['name'] }}</h2>
                                @if (($tool['status'] ?? 'live') !== 'live')
                                    <span class="nexo-badge-soon">{{ __('Próximamente') }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        @if (($tool['status'] ?? 'live') === 'live' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="nexo-btn nexo-btn--primary nexo-btn--sm">{{ __('Abrir') }}</a>
                        @endif
                        {{-- Alpine, not an inline onsubmit=: the CSP allow-lists
                             scripts by hash and never 'unsafe-inline', so an
                             inline handler would be dropped and confirm nothing. --}}
                        <form method="POST" action="{{ route('app.tools.destroy', $tool['key']) }}"
                              x-data
                              @submit="confirm(@js(__('¿Quitar :tool de tus herramientas?', ['tool' => $tool['name']]))) || $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="nexo-btn nexo-btn--ghost nexo-btn--sm">{{ __('Quitar') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if (count($available) > 0)
        {{-- Add from the registry. (AC-TOOLS-2) --}}
        <section id="anadir" class="mt-12 scroll-mt-8">
            <h2 class="mb-4 text-lg font-semibold">{{ __('Añadir herramientas') }}</h2>
            <ul class="grid gap-3 sm:grid-cols-2">
                @foreach ($available as $tool)
                    <li class="flex items-center gap-3 rounded-xl border border-line bg-surface p-3">
                        <img src="{{ $tool['mark'] }}" alt="" width="32" height="32" class="rounded-lg">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium">{{ $tool['name'] }}</p>
                            <p class="truncate text-xs text-muted">{{ $tool['tagline'] }}</p>
                        </div>
                        <form method="POST" action="{{ route('app.tools.store') }}">
                            @csrf
                            <input type="hidden" name="tool_key" value="{{ $tool['key'] }}">
                            <button type="submit" class="nexo-btn nexo-btn--ghost">{{ __('Añadir') }}</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-app-layout>
