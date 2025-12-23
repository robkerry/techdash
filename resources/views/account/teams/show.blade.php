<x-layouts.app>
    <x-navigation active="teams" />

    <x-page-header title="{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}">
        <x-slot name="actions">
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
        </x-slot>
    </x-page-header>

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
                                        <x-avatar :name="$member->name" size="sm" />
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
                            @if($websiteCount > 0)
                                <div class="mb-4 rounded-md bg-warning-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-warning-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-warning-800">
                                                Cannot delete team with websites
                                            </h3>
                                            <div class="mt-2 text-sm text-warning-700">
                                                <p>
                                                    This team cannot be deleted because it has <strong>{{ $websiteCount }} {{ Str::plural('website', $websiteCount) }}</strong> assigned to it.
                                                </p>
                                                <p class="mt-2">
                                                    To delete this team, you must first delete or reassign all websites. 
                                                    <a href="{{ route('websites.index') }}" class="font-medium underline hover:text-warning-900">
                                                        Manage websites →
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <x-button type="button" variant="danger" disabled>
                                    Delete Team
                                </x-button>
                            @else
                                <form method="POST" action="{{ route('account.teams.destroy', $team) }}" onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger">
                                        Delete Team
                                    </x-button>
                                </form>
                            @endif
                        </x-card>
                    @endif
                </div>
        </div>
    </main>
</x-layouts.app>

