<x-layouts.app>
    <x-navigation active="profile" />

    <header class="relative bg-white shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Account Settings</h1>
        </div>
    </header>

    <div class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-account-tabs active="profile" />
        </div>
    </div>

    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            @if (!auth()->user()->hasVerifiedEmail())
                <x-alert type="warning" class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <strong>Please verify your email address.</strong>
                            <p class="mt-1 text-sm">A verification link has been sent to your email address.</p>
                        </div>
                        <form method="POST" action="{{ route('verification.send') }}" class="ml-4">
                            @csrf
                            <x-button type="submit" variant="ghost" size="sm">
                                Resend verification email
                            </x-button>
                        </form>
                    </div>
                </x-alert>
            @endif

            <div class="space-y-6">
                <!-- Profile Information -->
                <x-card title="Profile Information" subtitle="Update your account's profile information and email address.">
                    <form method="POST" action="{{ route('account.profile.information.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <x-input
                            type="text"
                            name="name"
                            label="Name"
                            :value="old('name', $user->name)"
                            :error="$errors->updateProfileInformation->first('name')"
                            required
                            autocomplete="name"
                        />

                        <x-input
                            type="email"
                            name="email"
                            label="Email address"
                            :value="old('email', $user->email)"
                            :error="$errors->updateProfileInformation->first('email')"
                            required
                            autocomplete="email"
                        />

                        @if ($errors->updateProfileInformation->any())
                            <x-alert type="error">
                                @foreach ($errors->updateProfileInformation->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </x-alert>
                        @endif

                        <div>
                            <x-button type="submit" variant="primary">
                                Save Changes
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Update Password -->
                <x-card title="Update Password" subtitle="Ensure your account is using a long, random password to stay secure.">
                    <form method="POST" action="{{ route('account.profile.password.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <x-input
                            type="password"
                            name="current_password"
                            label="Current password"
                            :error="$errors->updatePassword->first('current_password')"
                            required
                            autocomplete="current-password"
                        />

                        <x-input
                            type="password"
                            name="password"
                            label="New password"
                            :error="$errors->updatePassword->first('password')"
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

                        @if ($errors->updatePassword->any())
                            <x-alert type="error">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </x-alert>
                        @endif

                        <div>
                            <x-button type="submit" variant="primary">
                                Update Password
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Two Factor Authentication -->
                <x-card title="Two Factor Authentication" subtitle="Add additional security to your account using two factor authentication.">
                    @if (!auth()->user()->two_factor_secret)
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                            </p>
                            <form method="POST" action="{{ route('account.two-factor.enable') }}">
                                @csrf
                                <x-button type="submit" variant="primary">
                                    Enable Two Factor Authentication
                                </x-button>
                            </form>
                        </div>
                    @else
                        <div class="space-y-4">
                            @if (auth()->user()->two_factor_confirmed_at)
                                <x-alert type="success">
                                    Two factor authentication is enabled and confirmed.
                                </x-alert>
                            @else
                                <x-alert type="warning">
                                    Please finish configuring two factor authentication below.
                                </x-alert>
                            @endif

                            <div>
                                <p class="text-sm font-medium text-gray-900 mb-2">Scan this QR code with your authenticator app:</p>
                                <div class="inline-block p-4 bg-white border border-gray-200 rounded-md">
                                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                </div>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-900 mb-2">Or enter this code manually:</p>
                                <div class="inline-block px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-mono text-sm">
                                    {{ decrypt(auth()->user()->two_factor_secret) }}
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Use this code if you cannot scan the QR code.</p>
                            </div>

                            @if (!auth()->user()->two_factor_confirmed_at)
                                <form method="POST" action="{{ route('account.two-factor.confirm') }}" class="space-y-4">
                                    @csrf
                                    <x-input
                                        type="text"
                                        name="code"
                                        label="Enter the code from your authenticator app"
                                        :error="$errors->first('code')"
                                        required
                                        autofocus
                                        placeholder="000000"
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
                                            Confirm
                                        </x-button>
                                        <x-button type="button" variant="ghost" onclick="document.getElementById('cancel-2fa-form').submit()">
                                            Cancel
                                        </x-button>
                                    </div>
                                </form>
                                <form id="cancel-2fa-form" method="POST" action="{{ route('account.two-factor.disable') }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @else
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 mb-2">Recovery Codes</p>
                                        <p class="text-sm text-gray-600 mb-4">
                                            Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                                        </p>
                                        @if (session('recovery_codes'))
                                            <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-4">
                                                <ul class="list-disc list-inside space-y-1 font-mono text-sm">
                                                    @foreach (json_decode(decrypt(session('recovery_codes')), true) as $code)
                                                        <li>{{ $code }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('account.two-factor.recovery-codes') }}">
                                            @csrf
                                            <x-button type="submit" variant="secondary" size="sm">
                                                Regenerate Recovery Codes
                                            </x-button>
                                        </form>
                                    </div>

                                    <form method="POST" action="{{ route('account.two-factor.disable') }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="danger">
                                            Disable Two Factor Authentication
                                        </x-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </main>
</x-layouts.app>

