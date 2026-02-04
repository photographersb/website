@props([
    'name' => '',
    'label' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'error' => null,
    'helper' => null,
    'options' => [],
])

@php
    $selectId = $name ?: 'select-' . uniqid();
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?: ($errors->has($name) ? $errors->first($name) : null);
    
    $selectClasses = 'block w-full px-4 py-3 pr-10 rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 appearance-none bg-white';
    
    if ($hasError) {
        $selectClasses .= ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500';
    } else {
        $selectClasses .= ' border-gray-300 text-gray-900 focus:border-primary-700 focus:ring-primary-500';
    }
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $selectId }}" class="block text-sm font-semibold text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $selectId }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except(['class', 'name', 'required']) }}
            class="{{ $selectClasses }}"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @if(is_array($options) && count($options) > 0)
                @foreach($options as $value => $text)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>
    
    @if($helper && !$hasError)
        <p class="mt-2 text-sm text-gray-500">{{ $helper }}</p>
    @endif
    
    @if($hasError)
        <p class="mt-2 text-sm text-red-600 flex items-start">
            <svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @endif
</div>
