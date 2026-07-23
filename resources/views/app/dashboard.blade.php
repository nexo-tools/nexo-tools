<x-guest-layout>
    <h1 class="mb-4 text-xl font-bold">{{ __('Tus herramientas') }}</h1>

    <ul class="space-y-2">
        @foreach ($tools as $tool)
            <li class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                @if ($tool['status'] === 'active' && $tool['url'])
                    <a href="{{ $tool['url'] }}" class="font-medium hover:underline">{{ $tool['name'] }}</a>
                @else
                    <span class="font-medium text-slate-500">{{ $tool['name'] }} · {{ __('Próximamente') }}</span>
                @endif
                <span class="block text-xs text-slate-500">{{ $tool['tagline'] }}</span>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:underline">{{ __('Cerrar sesión') }}</button>
    </form>
</x-guest-layout>
