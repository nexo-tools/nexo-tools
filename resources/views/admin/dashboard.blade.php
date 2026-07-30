<x-app-layout>
    <header class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Admin') }}</h1>
        <p class="mt-1 text-muted">{{ __('Cookieless metrics for the Nexo ecosystem.') }}</p>
    </header>

    @if ($totalVisits === 0)
        {{-- No data yet: a calm empty state, never an error. (AC-ADMIN-4) --}}
        <section class="rounded-2xl border border-line bg-surface-raised p-8 text-center">
            <p class="text-lg font-semibold">{{ __('No visit data yet.') }}</p>
        </section>
    @else
        {{-- Overview tiles --}}
        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-line bg-surface-raised p-5">
                <p class="text-sm text-muted">{{ __('Visits') }}</p>
                <p class="mt-1 text-3xl font-bold">{{ number_format($totalVisits) }}</p>
            </div>
            <div class="rounded-2xl border border-line bg-surface-raised p-5">
                <p class="text-sm text-muted">{{ __('Unique visitors') }}</p>
                <p class="mt-1 text-3xl font-bold">{{ number_format($totalUniques) }}</p>
            </div>
        </section>

        {{-- Per day (AC-ADMIN-2) --}}
        <section class="mt-10">
            <h2 class="mb-3 text-lg font-semibold">{{ __('Visits per day') }}</h2>
            <div class="overflow-x-auto rounded-xl border border-line">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-muted">
                        <tr>
                            <th class="p-3 text-left font-medium">{{ __('Day') }}</th>
                            <th class="p-3 text-right font-medium">{{ __('Visits') }}</th>
                            <th class="p-3 text-right font-medium">{{ __('Unique visitors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($byDay as $row)
                            <tr class="border-t border-line">
                                <td class="p-3">{{ $row->day }}</td>
                                <td class="p-3 text-right tabular-nums">{{ number_format($row->visits) }}</td>
                                <td class="p-3 text-right tabular-nums">{{ number_format($row->uniques) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Per tool (AC-ADMIN-2) --}}
        <section class="mt-10">
            <h2 class="mb-3 text-lg font-semibold">{{ __('By tool') }}</h2>
            <div class="overflow-x-auto rounded-xl border border-line">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-muted">
                        <tr>
                            <th class="p-3 text-left font-medium">{{ __('Tool') }}</th>
                            <th class="p-3 text-right font-medium">{{ __('Visits') }}</th>
                            <th class="p-3 text-right font-medium">{{ __('Unique visitors') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($byOrigin as $row)
                            <tr class="border-t border-line">
                                <td class="p-3">{{ $names[$row->origin] ?? $row->origin }}</td>
                                <td class="p-3 text-right tabular-nums">{{ number_format($row->visits) }}</td>
                                <td class="p-3 text-right tabular-nums">{{ number_format($row->uniques) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Top paths (AC-ADMIN-2) --}}
        <section class="mt-10">
            <h2 class="mb-3 text-lg font-semibold">{{ __('Top pages') }}</h2>
            <div class="overflow-x-auto rounded-xl border border-line">
                <table class="w-full text-sm">
                    <thead class="bg-surface text-muted">
                        <tr>
                            <th class="p-3 text-left font-medium">{{ __('Tool') }}</th>
                            <th class="p-3 text-left font-medium">{{ __('Page') }}</th>
                            <th class="p-3 text-right font-medium">{{ __('Visits') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topPaths as $row)
                            <tr class="border-t border-line">
                                <td class="p-3">{{ $names[$row->origin] ?? $row->origin }}</td>
                                <td class="max-w-xs truncate p-3 font-mono text-xs">{{ $row->path }}</td>
                                <td class="p-3 text-right tabular-nums">{{ number_format($row->visits) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- alvarocdev + referring tool (AC-ADMIN-3) --}}
        <section class="mt-10 rounded-2xl border border-line bg-surface-raised p-5">
            <h2 class="text-lg font-semibold">{{ __('Visits to alvarocdev') }}</h2>
            <div class="mt-3 flex flex-wrap gap-8">
                <div>
                    <p class="text-sm text-muted">{{ __('Visits') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ number_format($alvaroVisits) }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted">{{ __('Unique visitors') }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ number_format($alvaroUniques) }}</p>
                </div>
            </div>

            @if (count($alvaroRefs) > 0)
                <h3 class="mt-6 mb-2 text-sm font-semibold text-muted">{{ __('Referrals by tool') }}</h3>
                <ul class="space-y-1 text-sm">
                    @foreach ($alvaroRefs as $row)
                        <li class="flex justify-between border-t border-line py-2">
                            <span>{{ $names[$row->ref] ?? $row->ref }}</span>
                            <span class="tabular-nums">{{ number_format($row->visits) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif
</x-app-layout>
