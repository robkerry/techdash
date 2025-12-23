@props([
    'active' => 'dashboard', // dashboard, profile, teams, websites
])

<nav class="bg-gray-800" x-data="{ mobileMenuOpen: false, profileMenuOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/techdash-v2-logo-big.webp') }}" alt="{{ config('app.name') }}" class="h-6 w-auto">
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $active === 'dashboard' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('account.profile.edit') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $active === 'profile' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                            Profile
                        </a>
                        <a href="{{ route('account.teams.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $active === 'teams' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                            Teams
                        </a>
                        <a href="{{ route('websites.index') }}" class="rounded-md px-3 py-2 text-sm font-medium {{ $active === 'websites' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                            Websites
                        </a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <!-- Notifications button -->
                    <button type="button" class="relative rounded-full p-1 text-gray-400 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-primary-500 cursor-pointer">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                            <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="relative flex max-w-xs items-center rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 cursor-pointer">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            <x-avatar :name="auth()->user()->name" size="sm" />
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             x-cloak
                             class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg outline-1 outline-black/5">
                            <a href="{{ route('account.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your profile</a>
                            <a href="{{ route('account.teams.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Teams</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-primary-500">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <svg x-show="!mobileMenuOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="block md:hidden">
        <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
            <a href="{{ route('dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ $active === 'dashboard' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                Dashboard
            </a>
            <a href="{{ route('account.profile.edit') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ $active === 'profile' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                Profile
            </a>
            <a href="{{ route('account.teams.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ $active === 'teams' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                Teams
            </a>
            <a href="{{ route('websites.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ $active === 'websites' ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                Websites
            </a>
        </div>
        <div class="border-t border-white/10 pt-4 pb-3">
            <div class="flex items-center px-5">
                <div class="shrink-0">
                    <x-avatar :name="auth()->user()->name" size="md" />
                </div>
                <div class="ml-3">
                    <div class="text-base/5 font-medium text-white">{{ auth()->user()->name }}</div>
                    <div class="text-sm font-medium text-gray-400">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="{{ route('account.profile.edit') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">Your profile</a>
                <a href="{{ route('account.teams.index') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">Teams</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white cursor-pointer">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</nav>
