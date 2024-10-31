<x-header-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="min-h-full">
        <x-navbar />
        <header class="bg-white shadow">
            <div class="mx-auto max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $title }}</h1>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8">
                @if ($errors->any())
                    <x-alert type="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @elseif (session('success'))
                    <x-alert type="success">
                        {{ session('success') }}
                    </x-alert>
                @elseif (session('error'))
                    <x-alert type="error">
                        {{ session('error') }}
                    </x-alert>
                @endif
                {{ $slot }}
            </div>
        </main>
        @stack('scripts')
    </div>
</x-header-layout>
