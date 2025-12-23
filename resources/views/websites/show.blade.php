<x-layouts.app>
    <x-navigation active="websites" />

    <x-page-header title="{{ $website->name }}">
        <x-slot name="actions">
            <div class="flex space-x-2">
                <a href="{{ route('websites.edit', $website) }}">
                    <x-button variant="secondary" size="sm">
                        Edit
                    </x-button>
                </a>
                <a href="{{ route('websites.index') }}">
                    <x-button variant="ghost" size="sm">
                        Back
                    </x-button>
                </a>
            </div>
        </x-slot>
    </x-page-header>

    <main>
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <div class="space-y-6">
                <x-card title="Website Details">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $website->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">URL</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:text-primary-800 break-all">
                                    {{ $website->url }}
                                </a>
                            </dd>
                        </div>
                        @if($website->description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $website->description }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Team</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $website->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </x-card>

                <!-- Danger Zone -->
                <x-card title="Danger Zone" subtitle="Permanently delete this website.">
                    <form method="POST" action="{{ route('websites.destroy', $website) }}" onsubmit="return confirm('Are you sure you want to delete this website? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger">
                            Delete Website
                        </x-button>
                    </form>
                </x-card>
            </div>
        </div>
    </main>
</x-layouts.app>

