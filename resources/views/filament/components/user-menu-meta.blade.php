@if ($user)
    <div class="flex flex-col items-start leading-tight">
        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ $user->name }}
        </span>

        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ $role ?? 'User' }}
        </span>
    </div>
@endif
