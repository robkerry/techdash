<x-layouts.app>
    <x-navigation active="teams" />

    <x-page-header title="Edit Team" />

    <main>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">

                <x-card>
                    <form method="POST" action="{{ route('account.teams.update', $team) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <x-input
                            type="text"
                            name="name"
                            label="Team name"
                            :value="old('name', html_entity_decode($team->name, ENT_QUOTES, 'UTF-8'))"
                            :error="$errors->first('name')"
                            required
                            autofocus
                        />

                        @if ($errors->any())
                            <x-alert type="error">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </x-alert>
                        @endif

                        <div class="flex space-x-4">
                            <x-button type="submit" variant="primary">
                                Update Team
                            </x-button>
                            <a href="{{ route('account.teams.index') }}">
                                <x-button type="button" variant="ghost">
                                    Cancel
                                </x-button>
                            </a>
                        </div>
                    </form>
                </x-card>
        </div>
    </main>
</x-layouts.app>

