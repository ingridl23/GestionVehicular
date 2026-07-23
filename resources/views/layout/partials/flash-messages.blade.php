@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-green-100 border border-green-400 text-green-800 dark:bg-green-900 dark:border-green-600 dark:text-green-200 flex items-start gap-2">
    <i class="fas fa-check-circle mt-0.5"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-100 border border-red-400 text-red-800 dark:bg-red-900 dark:border-red-600 dark:text-red-200 flex items-start gap-2">
    <i class="fas fa-circle-exclamation mt-0.5"></i>
    <div>
        @if(is_array(session('error')))
            <strong>{{ session('error')['titulo'] ?? 'Error' }}</strong>
            @if(isset(session('error')['detalle']))
                <p class="text-sm mt-0.5">{{ session('error')['detalle'] }}</p>
            @endif
        @else
            {{ session('error') }}
        @endif
    </div>
</div>
@endif

@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-lg bg-red-100 border border-red-400 text-red-800 dark:bg-red-900 dark:border-red-600 dark:text-red-200">
    <div class="flex items-center gap-2 mb-1">
        <i class="fas fa-triangle-exclamation"></i>
        <strong>Corregí los siguientes errores:</strong>
    </div>
    <ul class="list-disc list-inside text-sm space-y-0.5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
