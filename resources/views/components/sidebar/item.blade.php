@props(['icon', 'label', 'route'])

<a href="{{ route($route) }}"
   class="flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200">
   <i class="fa-solid {{ $icon }} w-5"></i>
   <span>{{ $label }}</span>
</a>
