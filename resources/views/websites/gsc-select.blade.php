<x-layouts.app>
    <x-navigation active="websites" />

    <x-page-header title="Select Google Search Console Properties" />

    <main>
        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <x-alert type="success" dismissible class="mb-6">
                    {{ session('status') }}
                </x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error" class="mb-6">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </x-alert>
            @endif

            <x-card>
                <p class="mb-6 text-sm text-gray-600">
                    Select one or more verified properties from your Google Search Console account to add as websites.
                    Properties are grouped by hostname to avoid duplicates.
                </p>

                <form method="POST" action="{{ route('websites.gsc.store') }}" class="space-y-4">
                    @csrf

                    @if(empty($sites))
                        <div class="text-center py-8">
                            <p class="text-gray-500">No verified properties found in your Google Search Console account.</p>
                            <a href="{{ route('websites.create') }}" class="mt-4 inline-block">
                                <x-button variant="primary">
                                    Go Back
                                </x-button>
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($sites as $site)
                                <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="properties[]"
                                        value="{{ $site['siteUrl'] }}"
                                        class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                    >
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $site['normalized']['hostname'] }}
                                                </p>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $site['normalized']['url'] }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    GSC Property: {{ $site['siteUrl'] }}
                                                    @if($site['normalized']['is_domain_property'])
                                                        <span class="ml-2 inline-flex items-center rounded-full bg-info-100 px-2 py-0.5 text-xs font-medium text-info-800">
                                                            Domain Property
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="ml-4 inline-flex items-center rounded-full bg-success-100 px-2.5 py-0.5 text-xs font-medium text-success-800">
                                                {{ ucfirst(str_replace('site', '', strtolower($site['permissionLevel']))) }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex space-x-4 pt-6 border-t border-gray-200">
                            <x-button type="submit" variant="primary">
                                Add Selected Properties
                            </x-button>
                            <a href="{{ route('websites.create') }}">
                                <x-button type="button" variant="ghost">
                                    Cancel
                                </x-button>
                            </a>
                        </div>
                    @endif
                </form>
            </x-card>
        </div>
    </main>
</x-layouts.app>

