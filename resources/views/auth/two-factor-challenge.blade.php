<x-layouts.auth title="Two Factor Challenge">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-white">Two Factor Authentication</h2>
        <p class="mt-2 text-center text-sm text-gray-300">
            Please confirm access to your account by entering the authentication code provided by your authenticator application.
        </p>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <x-card>
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
                @csrf

                <x-input
                    type="text"
                    name="code"
                    label="Code"
                    :error="$errors->first('code')"
                    required
                    autofocus
                    autocomplete="one-time-code"
                    placeholder="000000"
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
                        Verify
                    </x-button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm leading-6">
                        <span class="bg-white px-6 text-gray-500">Or use a recovery code</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('two-factor.login') }}" class="mt-6 space-y-6">
                    @csrf

                    <x-input
                        type="text"
                        name="recovery_code"
                        label="Recovery Code"
                        :error="$errors->first('recovery_code')"
                        placeholder="xxxxxxxxxx"
                    />

                    @if ($errors->any())
                        <x-alert type="error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </x-alert>
                    @endif

                    <div>
                        <x-button type="submit" variant="secondary" fullWidth>
                            Use Recovery Code
                        </x-button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-layouts.auth>

