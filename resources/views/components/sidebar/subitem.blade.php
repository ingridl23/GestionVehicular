@props(['label','route'])

<a href="{{ route($route) }}"
    class="block px-3 py-2 rounded hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300">
    {{ $label }}
</a>
