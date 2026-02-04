@props([
    'type' => 'primary',
    'size' => 'md',
    'fullWidth' => false,
    'href' => null,
    'loading' => false,
    'disabled' => false,
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $sizeClasses = [
        'xs' => 'px-3 py-1.5 text-xs rounded-md',
        'sm' => 'px-4 py-2 text-sm rounded-lg',
        'md' => 'px-6 py-3 text-base rounded-lg',
        'lg' => 'px-8 py-4 text-lg rounded-xl',
    ][$size];
    
    $typeClasses = [
        'primary' => 'bg-primary-700 text-white hover:bg-primary-800 focus:ring-primary-500 shadow-md hover:shadow-lg',
        'secondary' => 'bg-gray-100 text-gray-900 hover:bg-gray-200 focus:ring-gray-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-md hover:shadow-lg',
        'outline' => 'bg-transparent border-2 border-primary-700 text-primary-700 hover:bg-primary-50 focus:ring-primary-500',
        'ghost' => 'bg-transparent text-primary-700 hover:bg-primary-50 focus:ring-primary-500',
        'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 shadow-md',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 shadow-md',
    ][$type];
    
    $widthClass = $fullWidth ? 'w-full' : '';
    
    $classes = trim("$baseClasses $sizeClasses $typeClasses $widthClass");
    
    $tag = $href ? 'a' : 'button';
    $attributes = $attributes->merge([
        'class' => $classes,
        'disabled' => $disabled || $loading,
    ]);
    
    if ($href) {
        $attributes = $attributes->merge(['href' => $href]);
    }
@endphp

<{{ $tag }} {{ $attributes }}>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @elseif($icon)
        <span class="mr-2">{!! $icon !!}</span>
    @endif
    
    {{ $slot }}
</{{ $tag }}>
