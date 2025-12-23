@php
    // Define the same color palette as the avatar component
    $colors = [
        ['bg' => 'bg-primary-600', 'text' => 'text-white', 'name' => 'Primary', 'sample' => 'Alice Adams'],
        ['bg' => 'bg-success-600', 'text' => 'text-white', 'name' => 'Success', 'sample' => 'Bob Brown'],
        ['bg' => 'bg-error-600', 'text' => 'text-white', 'name' => 'Error', 'sample' => 'Charlie Clark'],
        ['bg' => 'bg-warning-600', 'text' => 'text-white', 'name' => 'Warning', 'sample' => 'Diana Davis'],
        ['bg' => 'bg-info-600', 'text' => 'text-white', 'name' => 'Info', 'sample' => 'Edward Evans'],
        ['bg' => 'bg-purple-600', 'text' => 'text-white', 'name' => 'Purple', 'sample' => 'Fiona Foster'],
        ['bg' => 'bg-pink-600', 'text' => 'text-white', 'name' => 'Pink', 'sample' => 'George Green'],
        ['bg' => 'bg-indigo-600', 'text' => 'text-white', 'name' => 'Indigo', 'sample' => 'Hannah Harris'],
        ['bg' => 'bg-teal-600', 'text' => 'text-white', 'name' => 'Teal', 'sample' => 'Ian Ingram'],
        ['bg' => 'bg-cyan-600', 'text' => 'text-white', 'name' => 'Cyan', 'sample' => 'Julia Johnson'],
        ['bg' => 'bg-emerald-600', 'text' => 'text-white', 'name' => 'Emerald', 'sample' => 'Kevin King'],
        ['bg' => 'bg-amber-600', 'text' => 'text-white', 'name' => 'Amber', 'sample' => 'Laura Lee'],
        ['bg' => 'bg-violet-600', 'text' => 'text-white', 'name' => 'Violet', 'sample' => 'Michael Moore'],
        ['bg' => 'bg-fuchsia-600', 'text' => 'text-white', 'name' => 'Fuchsia', 'sample' => 'Nancy Nelson'],
        ['bg' => 'bg-rose-600', 'text' => 'text-white', 'name' => 'Rose', 'sample' => 'Oliver Owens'],
        ['bg' => 'bg-orange-600', 'text' => 'text-white', 'name' => 'Orange', 'sample' => 'Patricia Parker'],
    ];
    
    // Function to get initials (same as avatar component)
    function getInitials($name) {
        $nameParts = explode(' ', trim($name));
        if (count($nameParts) >= 2) {
            return strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[count($nameParts) - 1], 0, 1));
        } else {
            return strtoupper(substr($name, 0, min(2, strlen($name))));
        }
    }
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($colors as $color)
        <x-card>
            <div class="flex flex-col items-center space-y-4">
                <div class="rounded-full {{ $color['bg'] }} flex items-center justify-center {{ $color['text'] }} font-medium outline -outline-offset-1 outline-white/10 size-16 text-lg">
                    {{ getInitials($color['sample']) }}
                </div>
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-900">{{ $color['name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $color['sample'] }}</p>
                    <p class="text-xs text-gray-400 mt-1 font-mono">{{ $color['bg'] }}</p>
                </div>
            </div>
        </x-card>
    @endforeach
</div>

