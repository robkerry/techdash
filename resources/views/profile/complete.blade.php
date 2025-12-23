<x-layouts.app>
    <x-navigation active="profile" />

    <x-page-header title="Complete Your Profile" />

    <main>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            @if (session('warning'))
                <x-alert type="warning" dismissible class="mb-6">
                    {{ session('warning') }}
                </x-alert>
            @endif

            <x-card title="Complete Your Profile" subtitle="Please provide your name and set a password to continue.">
                <form method="POST" action="{{ route('account.profile.complete.store') }}" class="space-y-6">
                    @csrf

                    <x-input
                        type="text"
                        name="name"
                        label="Full name"
                        :value="old('name', $user->name)"
                        :error="$errors->first('name')"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <x-input
                        type="password"
                        name="password"
                        label="Password"
                        :error="$errors->first('password')"
                        required
                        autocomplete="new-password"
                    />

                    <x-input
                        type="password"
                        name="password_confirmation"
                        label="Confirm password"
                        required
                        autocomplete="new-password"
                    />

                    @if ($errors->any())
                        <x-alert type="error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </x-alert>
                    @endif

                    <div>
                        <x-button type="submit" variant="primary" fullWidth>
                            Complete Profile
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </main>
</x-layouts.app>

