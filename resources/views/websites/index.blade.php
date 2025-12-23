<x-layouts.app>
    <x-navigation active="websites" />

    <x-page-header title="Websites">
        <x-slot name="actions">
            <a href="{{ route('websites.gsc.connect') }}">
                <x-button variant="primary">
                    Connect GSC
                </x-button>
            </a>
        </x-slot>
    </x-page-header>

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    Managing websites for <strong>{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}</strong>
                </p>
            </div>

            @if($websites->isEmpty())
                <x-card>
                    <div class="text-center py-8">
                        <p class="text-gray-500">You don't have any websites yet.</p>
                        <p class="mt-2 text-sm text-gray-400">Connect your Google Search Console account to import verified websites.</p>
                        <a href="{{ route('websites.gsc.connect') }}" class="mt-4 inline-block">
                            <x-button variant="primary">
                                Connect Google Search Console
                            </x-button>
                        </a>
                    </div>
                </x-card>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($websites as $website)
                        <x-card>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $website->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 break-all">
                                        <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:text-primary-800">
                                            {{ $website->url }}
                                        </a>
                                    </p>
                                    @if($website->description)
                                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                            {{ $website->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('websites.show', $website) }}">
                                    <x-button variant="primary" size="sm">
                                        View
                                    </x-button>
                                </a>
                                <a href="{{ route('websites.edit', $website) }}">
                                    <x-button variant="secondary" size="sm">
                                        Edit
                                    </x-button>
                                </a>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</x-layouts.app>

