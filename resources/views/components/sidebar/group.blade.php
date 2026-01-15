@props(['icon','label'])

<div x-data="{ open: false }" class="space-y-1">
    <button @click="open = !open"
        class="flex items-center justify-between w-full px-3 py-2 rounded hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200">
        <div class="flex items-center gap-2">
            <i class="fa-solid {{ $icon }} w-5"></i>
            <span>{{ $label }}</span>
        </div>
        <i class="fa-solid fa-chevron-down" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="open" class="pl-8 space-y-1">
        {{ $slot }}
    </div>
</div>
