@php
    $user = filament()->auth()->user();
    $role = $user?->getRoleNames()?->first();
@endphp

@if ($user)
    <div class="me-2 hidden flex-col items-end leading-tight md:flex">
        <span class="text-lg font-extrabold text-black dark:text-gray-100">
            {{ $user->name }}
        </span>

        <span class="text-xs text-gray-500 dark:text-gray-400">
            ({{ $role ?? 'User' }})
        </span>
    </div>
@endif
