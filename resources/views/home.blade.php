<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    {{-- Display Validation and Exception Errors --}}
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
    <h2 class = "text-l">Selamat Datang Di Aplikasi Persediaan Toko X</h2>
</x-layout>
