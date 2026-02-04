@props([
    'name' => '',
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'helper' => null,
    'rows' => 4,
])

@php
    $textareaId = $name ?: 'textarea-' . uniqid();
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?: ($errors->has($name) ? $errors->first($name) : null);
    
    $textareaClasses = 'block w-full px-4 py-3 rounded-lg border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 resize-none';
    
    if ($hasError) {
        $textareaClasses .= ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 bg-red-50';
    } else {
        $textareaClasses .= ' border-gray-300 text-gray-900 focus:border-primary-700 focus:ring-primary-500 bg-white';
    }
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $textareaId }}" class="block text-sm font-semibold text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <textarea
        name="{{ $name }}"
        id="{{ $textareaId }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except(['class', 'name', 'placeholder', 'required', 'rows']) }}
        class="{{ $textareaClasses }}"
    >{{ $slot }}</textarea>
    
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
