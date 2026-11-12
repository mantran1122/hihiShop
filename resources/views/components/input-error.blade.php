@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs font-semibold text-rose-600 mt-1.5 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
