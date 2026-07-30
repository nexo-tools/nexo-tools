<x-app-layout>
    <header class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Your tools') }}</h1>
        <p class="mt-1 text-muted">{{ __('Launch any Nexo tool from here.') }}</p>
    </header>

    @if (count($added) === 0)
        {{-- Empty state: invite to explore the registry. (AC-TOOLS-4) --}}
        <section class="rounded-2xl border border-line bg-surface-raised p-8 text-center">
            <p class="text-lg font-semibold">{{ __('You haven\'t added any tools yet') }}</p>
            <p class="mx-auto mt-2 max-w-md text-sm text-muted">{{ __('Explore the Nexo ecosystem and add the ones you use to keep them always at hand.') }}</p>
            @if (count($available) > 0)
                <a href="#anadir" class="nexo-btn nexo-btn--primary mt-5">{{ __('Add tools') }}</a>
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
                                    <span class="nexo-badge-soon">{{ __('Coming soon') }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-muted">{{ $tool['tagline'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        @if (($tool['status'] ?? 'live') === 'live' && $tool['url'])
                            <a href="{{ $tool['url'] }}" class="nexo-btn nexo-btn--primary nexo-btn--sm">{{ __('Open') }}</a>
                        @endif
                        {{-- Alpine, not an inline onsubmit=: the CSP allow-lists
                             scripts by hash and never 'unsafe-inline', so an
                             inline handler would be dropped and confirm nothing. --}}
                        <form method="POST" action="{{ route('app.tools.destroy', $tool['key']) }}"
                              x-data
                              @submit="confirm(@js(__('Remove :tool from your tools?', ['tool' => $tool['name']]))) || $event.preventDefault()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="nexo-btn nexo-btn--ghost nexo-btn--sm">{{ __('Remove') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if (count($available) > 0)
        {{-- Add from the registry. (AC-TOOLS-2) --}}
        <section id="anadir" class="mt-12 scroll-mt-8">
            <h2 class="mb-4 text-lg font-semibold">{{ __('Add tools') }}</h2>
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
                            <button type="submit" class="nexo-btn nexo-btn--ghost">{{ __('Add') }}</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-app-layout>
