<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('store.name', '{BRAND}') }}</title>
    <meta name="description" content="{{ $description ?? config('store.description', '') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white" x-data="{ locale: '{{ app()->getLocale() }}', switchLocale(locale) { fetch('{{ route('locale.switch') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ locale }) }).then(() => { this.locale = locale; location.reload(); }); } }">
    @include('components.storefront.announcement-bar')
    @include('components.storefront.header')
    
    <main class="flex-grow">
        @yield('content')
    </main>

    @include('components.storefront.footer')
    @include('components.storefront.whatsapp-float')

    @stack('scripts')
    
    <script type="module">
        import Alpine from 'alpinejs';
        import collapse from '@alpinejs/collapse';
        import focus from '@alpinejs/focus';
        window.Alpine = Alpine;
        Alpine.plugin(collapse);
        Alpine.plugin(focus);
        Alpine.start();
    </script>
    @livewireScripts
</body>
</html>