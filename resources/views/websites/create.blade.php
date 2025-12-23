<x-layouts.app>
    <x-navigation active="websites" />

    <x-page-header title="Add Websites" />

    <main>
        <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
            @if ($errors->has('gsc'))
                <x-alert type="error" class="mb-6">
                    {{ $errors->first('gsc') }}
                </x-alert>
            @endif

            <!-- Google Search Console Connection -->
            <x-card>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Import from Google Search Console</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Connect your Google Search Console account to import verified websites. 
                        Only websites that are verified in your GSC account can be added.
                    </p>
                    <a href="{{ route('websites.gsc.connect') }}">
                        <x-button variant="primary" size="lg">
                            Connect Google Search Console
                        </x-button>
                    </a>
                </div>
            </x-card>
        </div>
    </main>
</x-layouts.app>

