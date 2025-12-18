<x-layouts.app>
    <x-navigation active="teams" />

    <header class="relative bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Account Settings</h1>
                <a href="{{ route('account.teams.create') }}">
                    <x-button variant="primary">
                        Create Team
                    </x-button>
                </a>
            </div>
        </div>
    </header>

    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-account-tabs active="teams" />
        </div>
    </div>

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($teams as $team)
                        <x-card>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Owner: {{ $team->owner->name }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $team->memberCount() }} {{ Str::plural('member', $team->memberCount()) }}
                                    </p>
                                </div>
                                @if($currentTeam && $currentTeam->id === $team->id)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800">
                                        Current
                                    </span>
                                @endif
                            </div>

                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('account.teams.show', $team) }}" class="flex-1">
                                    <x-button variant="primary" size="sm" fullWidth>
                                        View
                                    </x-button>
                                </a>
                                @if($team->isOwnedBy(auth()->user()))
                                    <a href="{{ route('account.teams.edit', $team) }}">
                                        <x-button variant="secondary" size="sm">
                                            Edit
                                        </x-button>
                                    </a>
                                @endif
                                @if($currentTeam && $currentTeam->id !== $team->id)
                                    <form method="POST" action="{{ route('account.teams.switch', $team) }}" class="inline">
                                        @csrf
                                        <x-button type="submit" variant="ghost" size="sm">
                                            Switch
                                        </x-button>
                                    </form>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>

                @if($teams->isEmpty())
                    <x-card>
                        <div class="text-center py-8">
                            <p class="text-gray-500">You don't have any teams yet.</p>
                            <a href="{{ route('account.teams.create') }}" class="mt-4 inline-block">
                                <x-button variant="primary">
                                    Create Your First Team
                                </x-button>
                            </a>
                        </div>
                    </x-card>
                @endif
        </div>
    </main>
</x-layouts.app>

