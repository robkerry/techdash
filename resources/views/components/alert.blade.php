@props([
    'type' => 'info', // success, error, warning, info
    'dismissible' => false,
])

@php
    $borderClasses = [
        'success' => 'border-l-4 border-success-700',
        'error' => 'border-l-4 border-error-700',
        'warning' => 'border-l-4 border-warning-700',
        'info' => 'border-l-4 border-info-700',
    ];
    
    $bgClasses = [
        'success' => 'bg-success-50',
        'error' => 'bg-error-50',
        'warning' => 'bg-warning-50',
        'info' => 'bg-info-50',
    ];
    
    $textClasses = [
        'success' => 'text-success-700',
        'error' => 'text-error-700',
        'warning' => 'text-warning-700',
        'info' => 'text-info-700',
    ];
    
    // Icons use the same color as text
    $iconClasses = [
        'success' => 'text-success-700',
        'error' => 'text-error-700',
        'warning' => 'text-warning-700',
        'info' => 'text-info-700',
    ];
    
    $icons = [
        'success' => '<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg>',
        'error' => '<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" /></svg>',
        'warning' => '<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5"><path d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" fill-rule="evenodd" /></svg>',
        'info' => '<svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-cloak x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-6 {{ $borderClasses[$type] }} {{ $bgClasses[$type] }} p-4" role="alert">
    <div class="flex items-center">
        <div class="shrink-0 {{ $iconClasses[$type] }}">
            {!! $icons[$type] !!}
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm {{ $textClasses[$type] }}">
                {{ $slot }}
            </p>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3 shrink-0">
                <button @click="show = false" type="button" class="inline-flex rounded-md p-1.5 {{ $textClasses[$type] }} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-{{ $type }}-50 focus:ring-{{ $type }}-600">
                    <span class="sr-only">Dismiss</span>
                    <svg class="size-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>

