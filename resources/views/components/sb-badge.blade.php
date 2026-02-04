@props([
    'type' => 'default',
    'size' => 'md',
    'rounded' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-semibold';
    
    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ][$size];
    
    $typeClasses = [
        'default' => 'bg-gray-100 text-gray-800',
        'primary' => 'bg-primary-100 text-primary-700',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        
        // Status badges
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-600',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'draft' => 'bg-gray-100 text-gray-600',
        'published' => 'bg-blue-100 text-blue-800',
        'verified' => 'bg-primary-100 text-primary-700',
    ][$type];
    
    $roundedClass = $rounded ? 'rounded-full' : 'rounded-md';
    
    $classes = trim("$baseClasses $sizeClasses $typeClasses $roundedClass");
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
