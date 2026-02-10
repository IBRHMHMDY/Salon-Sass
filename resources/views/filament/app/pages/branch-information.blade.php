<x-filament-panels::page>
    @if ($branch)
        <x-filament::section
            heading="بيانات الفرع الحالي"
            description="المعلومات الأساسية للفرع المختار من قائمة الفروع بالأعلى"
        >
            <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($details as $item)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900/40">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ $item['label'] }}
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $item['value'] }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>
    @else
        <x-filament::section heading="معلومات الفرع">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                لا يوجد فرع محدد حاليًا.
            </p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
