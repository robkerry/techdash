@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => null,
    'error' => null,
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'autocomplete' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error !== null;
    $baseClasses = 'block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600 sm:text-sm/6 transition-colors';
    $inputClasses = $baseClasses . ' ' . ($hasError 
        ? 'outline-error-500' 
        : '');
    
    // Handle autocomplete from prop or attribute
    $autocompleteValue = $autocomplete ?? $attributes->get('autocomplete');
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm/6 font-medium text-gray-900">
            {{ $label }}
            @if($required)
                <span class="text-error-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="{{ $label ? 'mt-2' : '' }}">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            @if($autocompleteValue) autocomplete="{{ $autocompleteValue }}" @endif
            {{ $attributes->except(['class', 'type', 'name', 'id', 'value', 'placeholder', 'autocomplete'])->merge(['class' => $inputClasses]) }}
        >
    </div>
    
    @if($hasError)
        <p class="mt-1.5 text-sm text-error-600">{{ $error }}</p>
    @endif
</div>

