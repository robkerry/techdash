<x-layouts.app>
    <x-navigation active="dashboard" />

    <x-page-header title="Dashboard" />

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-card title="Email Verified" subtitle="{{ auth()->user()->hasVerifiedEmail() ? 'Yes' : 'No' }}">
                    @if(auth()->user()->hasVerifiedEmail())
                        <div class="flex items-center text-success-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Your email is verified
                        </div>
                    @else
                        <div class="flex items-center text-warning-600">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Please verify your email
                        </div>
                    @endif
                </x-card>

                <x-card title="Account Created" subtitle="{{ auth()->user()->created_at->format('M d, Y') }}">
                    <p class="text-sm text-gray-600">Member since {{ auth()->user()->created_at->diffForHumans() }}</p>
                </x-card>

                <x-card title="Current Team" subtitle="{{ auth()->user()->currentTeam?->name ?? 'No team' }}">
                    <p class="text-sm text-gray-600">{{ auth()->user()->teams()->count() }} {{ Str::plural('team', auth()->user()->teams()->count()) }} total</p>
                </x-card>
            </div>
        </div>
    </main>
</x-layouts.app>


