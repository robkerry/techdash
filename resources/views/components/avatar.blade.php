@props([
    'name',
    'size' => 'md', // sm, md, lg
    'class' => '',
])

@php
    // Extract initials from name (e.g., "John Knight" -> "JK")
    $nameParts = explode(' ', trim($name));
    $initials = '';
    if (count($nameParts) >= 2) {
        // First letter of first name + first letter of last name
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
    } else {
        // Single name, use first two letters
        $initials = strtoupper(substr($name, 0, min(2, strlen($name))));
    }

    // Hash the name to get a consistent color
    $hash = crc32(strtolower(trim($name)));
    
    // Define a palette of complementary colors
    // Using a mix of custom theme colors and standard Tailwind colors for variety
    $colors = [
        ['bg' => 'bg-primary-600', 'text' => 'text-white'],
        ['bg' => 'bg-success-600', 'text' => 'text-white'],
        ['bg' => 'bg-error-600', 'text' => 'text-white'],
        ['bg' => 'bg-warning-600', 'text' => 'text-white'],
        ['bg' => 'bg-info-600', 'text' => 'text-white'],
        ['bg' => 'bg-purple-600', 'text' => 'text-white'],
        ['bg' => 'bg-pink-600', 'text' => 'text-white'],
        ['bg' => 'bg-indigo-600', 'text' => 'text-white'],
        ['bg' => 'bg-teal-600', 'text' => 'text-white'],
        ['bg' => 'bg-cyan-600', 'text' => 'text-white'],
        ['bg' => 'bg-emerald-600', 'text' => 'text-white'],
        ['bg' => 'bg-amber-600', 'text' => 'text-white'],
        ['bg' => 'bg-violet-600', 'text' => 'text-white'],
        ['bg' => 'bg-fuchsia-600', 'text' => 'text-white'],
        ['bg' => 'bg-rose-600', 'text' => 'text-white'],
        ['bg' => 'bg-orange-600', 'text' => 'text-white'],
    ];
    
    // Pick a color based on the hash
    $colorIndex = abs($hash) % count($colors);
    $selectedColor = $colors[$colorIndex];
    
    // Size classes
    $sizeClasses = [
        'sm' => 'size-8 text-xs',
        'md' => 'size-10 text-sm',
        'lg' => 'size-12 text-base',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="rounded-full {{ $selectedColor['bg'] }} flex items-center justify-center {{ $selectedColor['text'] }} font-medium outline -outline-offset-1 outline-white/10 {{ $sizeClass }} {{ $class }}">
    {{ $initials }}
</div>

