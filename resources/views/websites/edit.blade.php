<x-layouts.app>
    <x-navigation active="websites" />

    <x-page-header title="Edit Website" />

    <main>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('websites.update', $website) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <x-input
                        type="text"
                        name="name"
                        label="Website name"
                        :value="old('name', $website->name)"
                        :error="$errors->first('name')"
                        required
                        autofocus
                    />

                    <!-- Read-only fields -->
                    <div>
                        <x-label for="url" value="URL" />
                        <input
                            type="text"
                            id="url"
                            value="{{ $website->url }}"
                            disabled
                            class="block w-full rounded-md bg-gray-50 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6 cursor-not-allowed"
                        >
                        <p class="mt-1.5 text-xs text-gray-400">URL is managed by Google Search Console and cannot be changed.</p>
                    </div>

                    @if($website->description)
                        <div>
                            <x-label for="description" value="Description" />
                            <div class="block w-full rounded-md bg-gray-50 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">
                                {{ $website->description }}
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400">Description is managed by Google Search Console and cannot be changed.</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <x-alert type="error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </x-alert>
                    @endif

                    <div class="flex space-x-4">
                        <x-button type="submit" variant="primary">
                            Update Website
                        </x-button>
                        <a href="{{ route('websites.index') }}">
                            <x-button type="button" variant="ghost">
                                Cancel
                            </x-button>
                        </a>
                    </div>
                </form>
            </x-card>
        </div>
    </main>
</x-layouts.app>

