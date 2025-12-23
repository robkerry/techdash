<x-layouts.app>
    <x-navigation active="dashboard" />

    <x-page-header title="Avatar Color Showcase" />

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm text-gray-600">
                    This page displays all available avatar color options. Each avatar uses a hash of the user's name to consistently assign a color from the palette.
                </p>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">All Color Options</h2>
                <x-avatar-showcase />
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Size Variations</h2>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <x-avatar name="John Knight" size="sm" />
                        <p class="text-xs text-gray-500 mt-2">Small (sm)</p>
                    </div>
                    <div class="text-center">
                        <x-avatar name="John Knight" size="md" />
                        <p class="text-xs text-gray-500 mt-2">Medium (md)</p>
                    </div>
                    <div class="text-center">
                        <x-avatar name="John Knight" size="lg" />
                        <p class="text-xs text-gray-500 mt-2">Large (lg)</p>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Example Names</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach(['Alice Adams', 'Bob Brown', 'Charlie Clark', 'Diana Davis', 'Edward Evans', 'Fiona Foster', 'George Green', 'Hannah Harris', 'Ian Ingram', 'Julia Johnson', 'Kevin King', 'Laura Lee'] as $name)
                        <div class="text-center">
                            <x-avatar :name="$name" size="md" />
                            <p class="text-xs text-gray-600 mt-2">{{ $name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</x-layouts.app>

