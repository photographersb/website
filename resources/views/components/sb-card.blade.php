@props([
    'padding' => 'md',
    'shadow' => true,
    'hover' => false,
])

@php
    $baseClasses = 'bg-white rounded-lg border border-gray-200 transition-all duration-200';
    
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ][$padding];
    
    $shadowClass = $shadow ? 'shadow-md' : '';
    $hoverClass = $hover ? 'hover:shadow-lg hover:border-primary-300 cursor-pointer' : '';
    
    $classes = trim("$baseClasses $paddingClasses $shadowClass $hoverClass");
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
