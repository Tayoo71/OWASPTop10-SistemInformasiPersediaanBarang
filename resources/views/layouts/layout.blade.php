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
                <x-display-error />
                {{ $slot }}
            </div>
        </main>
    </div>
</x-header-layout>
