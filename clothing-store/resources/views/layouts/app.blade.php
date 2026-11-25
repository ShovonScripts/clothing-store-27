<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('store.name', '{BRAND}') }}</title>
    <meta name="description" content="{{ $description ?? config('store.description', '') }}">

    @php
        $livewireRoutes = ['shop', 'product.show', 'cart.page', 'checkout', 'order.success', 'track.order'];
        $needsLivewire = in_array(request()->route()?->getName(), $livewireRoutes, true);
        $metaPixel = app(\App\Services\MetaPixelService::class);
    @endphp

    @vite('resources/css/app.css')

    @if($needsLivewire)
        @vite('resources/js/app.js')
        @livewireStyles
    @else
        @vite('resources/js/alpine.js')
    @endif

    @stack('styles')

    @if($metaPixel->isEnabled())
        {!! $metaPixel->getBaseScript() !!}
    @endif
</head>
<body class="min-h-screen flex flex-col bg-white" x-data="{ locale: '{{ app()->getLocale() }}', switchLocale(locale) { fetch('{{ route('locale.switch') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ locale }) }).then(() => { this.locale = locale; location.reload(); }); } }">
    <script>
        window.__alpineInitCount = 0;
        document.addEventListener('alpine:init', () => { window.__alpineInitCount++; });
    </script>
    @include('components.storefront.announcement-bar')
    @include('components.storefront.header')

    <main class="flex-grow">
        @yield('content')
        {!! $slot ?? '' !!}
    </main>

    @include('components.storefront.footer')
    @include('components.storefront.whatsapp-float')

    @stack('scripts')

    <script>
        document.addEventListener('cart-count-changed', function (event) {
            var badge = document.getElementById('cart-count');
            if (badge && event.detail && typeof event.detail.count !== 'undefined') {
                badge.textContent = event.detail.count;
                badge.classList.toggle('hidden', Number(event.detail.count) === 0);
            }
        });

        // Meta Pixel event dispatcher
        window.MetaPixel = {
            fire: function(eventName, params, eventId) {
                if (typeof fbq !== 'undefined') {
                    fbq('track', eventName, params, {eventID: eventId});
                }
            }
        };

        // Listen for custom events from Livewire/Alpine
        document.addEventListener('meta-pixel:view-content', function(e) {
            window.MetaPixel.fire('ViewContent', e.detail.params, e.detail.eventId);
        });
        document.addEventListener('meta-pixel:add-to-cart', function(e) {
            window.MetaPixel.fire('AddToCart', e.detail.params, e.detail.eventId);
        });
        document.addEventListener('meta-pixel:initiate-checkout', function(e) {
            window.MetaPixel.fire('InitiateCheckout', e.detail.params, e.detail.eventId);
        });
        document.addEventListener('meta-pixel:add-payment-info', function(e) {
            window.MetaPixel.fire('AddPaymentInfo', e.detail.params, e.detail.eventId);
        });
        document.addEventListener('meta-pixel:lead', function(e) {
            window.MetaPixel.fire('Lead', e.detail.params, e.detail.eventId);
        });
    </script>

    @if($needsLivewire)
        @livewireScripts
    @endif
</body>
</html>
