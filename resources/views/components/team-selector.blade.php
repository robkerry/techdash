@php
    $user = auth()->user();
    $teams = $user->teams;
    $currentTeam = $user->currentTeam;
@endphp

<div class="relative" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        type="button"
        class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-2 focus:outline-offset-2 focus:outline-primary-600"
    >
        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
            <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.615 16.428a1.224 1.224 0 0 1-.569-1.175 6.002 6.002 0 0 1 11.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 0 1 7 18a9.953 9.953 0 0 1-5.385-1.572ZM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 0 0-1.588-3.755 4.502 4.502 0 0 1 5.874 2.636.818.818 0 0 1-.36.98A7.465 7.465 0 0 1 14.5 16Z" />
        </svg>
        <span>{{ html_entity_decode($currentTeam?->name ?? 'No Team', ENT_QUOTES, 'UTF-8') }}</span>
        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" :class="{ 'rotate-180': open }">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div 
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white py-1 shadow-lg outline-1 outline-black/5"
    >
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Teams
        </div>
        
        @forelse($teams as $team)
            <form method="POST" action="{{ route('account.teams.switch', $team) }}" class="block">
                @csrf
                <input type="hidden" name="redirect" value="{{ url()->current() }}">
                <button 
                    type="submit"
                    class="w-full text-left px-4 py-2 text-sm {{ $currentTeam && $currentTeam->id === $team->id ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}"
                >
                    <div class="flex items-center justify-between">
                        <span>{{ html_entity_decode($team->name, ENT_QUOTES, 'UTF-8') }}</span>
                        @if($currentTeam && $currentTeam->id === $team->id)
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 text-primary-600">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                </button>
            </form>
        @empty
            <div class="px-4 py-2 text-sm text-gray-500">
                No teams available
            </div>
        @endforelse
        
        <div class="border-t border-gray-200 my-1"></div>
        
        <a 
            href="{{ route('account.teams.create') }}"
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
        >
            <div class="flex items-center gap-2">
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                <span>Create Team</span>
            </div>
        </a>
    </div>
</div>

