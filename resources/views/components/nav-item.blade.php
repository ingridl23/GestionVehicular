{{-- resources/views/components/nav-item.blade.php --}}
@props(['icon', 'label', 'route', 'active' => false])

<a
    href="{{ route($route) }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
           {{ $active
              ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
    {{ $attributes }}
>
    <i class="fas {{ $icon }} w-5 text-center"></i>
    <span x-show="sidebarOpen" class="text-sm font-medium">{{ $label }}</span>
</a>
