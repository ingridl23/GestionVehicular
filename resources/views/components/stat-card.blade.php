<!-- resources/views/components/stat-card.blade.php -->
@props([
    'title',
    'value',
    'icon',
    'trend' => null,
    'trendUp' => true,
    'color' => 'blue'
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
        'green' => 'bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400',
        'yellow' => 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400',
        'red' => 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400',
        'purple' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $value }}</p>

            @if($trend)
                <div class="flex items-center gap-1 mt-3">
                    <i class="fas fa-arrow-{{ $trendUp ? 'up' : 'down' }} text-xs {{ $trendUp ? 'text-green-500' : 'text-red-500' }}"></i>
                    <span class="text-sm {{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $trend }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">vs mes anterior</span>
                </div>
            @endif
        </div>

        <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $colorClasses[$color] ?? $colorClasses['blue'] }}">
            <i class="fas {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>
