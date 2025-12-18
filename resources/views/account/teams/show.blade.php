<x-layouts.app>
    <x-navigation active="teams" />

    <header class="relative bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}</h1>
                <div class="flex space-x-2">
                    @if($isOwner)
                        <a href="{{ route('account.teams.edit', $team) }}">
                            <x-button variant="secondary" size="sm">
                                Edit
                            </x-button>
                        </a>
                    @endif
                    <a href="{{ route('account.teams.index') }}">
                        <x-button variant="ghost" size="sm">
                            Back
                        </x-button>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error" class="mb-6">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </x-alert>
            @endif

                <div class="space-y-6">
                    <!-- Team Members -->
                    <x-card title="Team Members" subtitle="{{ $team->memberCount() }} {{ Str::plural('member', $team->memberCount()) }}">
                        <div class="space-y-4">
                            @foreach($team->users as $member)
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            @if($team->owner_id === $member->id)
                                                <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800">
                                                    Owner
                                                </span>
                                            @endif
                                            @if(auth()->user()->current_team_id === $team->id && auth()->id() === $member->id)
                                                <span class="inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-medium text-success-800">
                                                    Current Team
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isOwner && $team->owner_id !== $member->id)
                                        <form method="POST" action="{{ route('account.teams.members.destroy', [$team, $member]) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="ghost" size="sm">
                                                Remove
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </x-card>

                    @if($isOwner)
                        <!-- Invite Members -->
                        <x-card title="Invite Members" subtitle="Send invitations to join this team.">
                            <form method="POST" action="{{ route('account.teams.invitations.store', $team) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="email" class="block text-sm/6 font-medium text-gray-900 mb-2">
                                        Email address
                                        <span class="text-error-500">*</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <input
                                                type="email"
                                                name="email"
                                                id="email"
                                                value="{{ old('email') }}"
                                                placeholder="user@example.com"
                                                required
                                                autocomplete="email"
                                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600 sm:text-sm/6 transition-colors {{ $errors->first('email') ? 'outline-error-500' : '' }}"
                                            >
                                            @if($errors->first('email'))
                                                <p class="mt-1.5 text-sm text-error-600">{{ $errors->first('email') }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <x-button type="submit" variant="primary">
                                                Send Invitation
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </x-card>

                        <!-- Pending Invitations -->
                        @if($invites->isNotEmpty())
                            <x-card title="Pending Invitations" subtitle="{{ $invites->count() }} {{ Str::plural('invitation', $invites->count()) }}">
                                <div class="space-y-4">
                                    @foreach($invites as $invite)
                                        <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $invite->email }}</p>
                                                <p class="text-sm text-gray-500">Invited {{ $invite->created_at->diffForHumans() }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('account.teams.invitations.destroy', [$team, $invite->id]) }}" onsubmit="return confirm('Are you sure you want to cancel this invitation?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="ghost" size="sm">
                                                    Cancel
                                                </x-button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </x-card>
                        @endif

                        <!-- Danger Zone -->
                        <x-card title="Danger Zone" subtitle="Permanently delete this team.">
                            <form method="POST" action="{{ route('account.teams.destroy', $team) }}" onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger">
                                    Delete Team
                                </x-button>
                            </form>
                        </x-card>
                    @endif
                </div>
        </div>
    </main>
</x-layouts.app>

