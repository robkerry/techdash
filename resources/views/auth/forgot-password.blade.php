<x-layouts.auth>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-white">Reset your password</h2>
        <p class="mt-2 text-center text-sm/6 text-gray-300">Enter your email address and we'll send you a link to reset your password.</p>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
            @if (session('status'))
                <x-alert type="success" class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                    <div class="mt-2">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary-600 sm:text-sm/6 {{ $errors->first('email') ? 'outline-error-500' : '' }}"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-error-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($errors->any())
                    <x-alert type="error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </x-alert>
                @endif

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-primary-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-primary-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">Send password reset link</button>
                </div>
            </form>
        </div>

        <p class="mt-10 text-center text-sm/6 text-gray-300">
            <a href="{{ route('login') }}" class="font-semibold text-primary-400 hover:text-primary-300">Back to sign in</a>
        </p>
    </div>
</x-layouts.auth>

