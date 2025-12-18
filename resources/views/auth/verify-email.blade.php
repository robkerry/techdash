<x-layouts.auth>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Verify your email address</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
            @if (session('status') == 'verification-link-sent')
                <x-alert type="success" class="mb-6">
                    A new verification link has been sent to your email address.
                </x-alert>
            @endif

            <div class="text-center space-y-6">
                <p class="text-sm/6 text-gray-600">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we'll gladly send you another.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <form method="POST" action="{{ route('verification.send') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="primary">
                            Resend verification email
                        </x-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <x-button type="submit" variant="ghost">
                            Log out
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>

