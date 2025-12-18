@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'error' => null,
    'checked' => false,
    'value' => '1',
])

@php
    $inputId = $id ?? $name;
    $hasError = $error !== null;
@endphp

<div>
    <div class="flex items-center">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ $value }}"
            {{ old($name) || $checked ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500' . ($hasError ? ' border-error-300' : '')]) }}
        >
        @if($label)
            <label for="{{ $inputId }}" class="ml-2 block text-sm text-gray-900">
                {{ $label }}
            </label>
        @endif
    </div>
    @if($hasError)
        <p class="mt-1.5 text-sm text-error-600">{{ $error }}</p>
    @endif
</div>

