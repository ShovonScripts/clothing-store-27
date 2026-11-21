@extends('layouts.app')

@section('title', __('Checkout') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Checkout') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('Enter your details to complete your order') }}</p>
            </header>

            <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf

                <div class="lg:col-span-2 space-y-6">
                    <section aria-labelledby="contact-heading" class="bg-white rounded-xl border border-gray-100 p-6">
                        <h2 id="contact-heading" class="text-lg font-semibold text-gray-900 mb-4">{{ __('Contact Information') }}</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" required autocomplete="email" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="you@example.com">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone') }} <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" id="phone" required autocomplete="tel" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="+880 1XXX XXXXXX">
                            </div>
                        </div>
                    </section>

                    <section aria-labelledby="shipping-heading" class="bg-white rounded-xl border border-gray-100 p-6">
                        <h2 id="shipping-heading" class="text-lg font-semibold text-gray-900 mb-4">{{ __('Shipping Address') }}</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="first_name" required autocomplete="given-name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="last_name" required autocomplete="family-name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Address') }} <span class="text-red-500">*</span></label>
                            <textarea name="address" id="address" required autocomplete="street-address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="House/Road/Block"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="district" class="block text-sm font-medium text-gray-700 mb-1">{{ __('District') }} <span class="text-red-500">*</span></label>
                                <select name="district" id="district" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">{{ __('Select District') }}</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="area" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Area/Thana') }} <span class="text-red-500">*</span></label>
                                <select name="area" id="area" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">{{ __('Select Area') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" id="postal_code" autocomplete="postal-code" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="1212">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Order Notes') }}</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="{{ __('Special delivery instructions...') }}"></textarea>
                        </div>
                    </section>

                    <section aria-labelledby="payment-heading" class="bg-white rounded-xl border border-gray-100 p-6">
                        <h2 id="payment-heading" class="text-lg font-semibold text-gray-900 mb-4">{{ __('Payment Method') }}</h2>

                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-primary/5 transition-colors">
                                <input type="radio" name="payment_method" value="cod" checked class="w-4 h-4 text-primary focus:ring-primary border-gray-300">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ __('Cash on Delivery') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Pay when you receive your order. Available everywhere in Bangladesh.') }}</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-primary/5 transition-colors opacity-50">
                                <input type="radio" name="payment_method" value="bkash" disabled class="w-4 h-4 text-primary focus:ring-primary border-gray-300">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ __('bKash') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Pay via bKash. Coming soon.') }}</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary hover:bg-primary/5 transition-colors opacity-50">
                                <input type="radio" name="payment_method" value="nagad" disabled class="w-4 h-4 text-primary focus:ring-primary border-gray-300">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ __('Nagad') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Pay via Nagad. Coming soon.') }}</p>
                                </div>
                            </label>
                        </div>
                    </section>
                </div>

                <div class="lg:col-span-1">
                    <aside class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24" aria-labelledby="summary-heading">
                        <h2 id="summary-heading" class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Summary') }}</h2>

                        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                            @foreach($items as $item)
                                <div class="flex gap-3">
                                    <a href="{{ route('product.show', $item['variant']->product->slug) }}" class="relative w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                        @if($item['variant']->product->getFirstImageUrl('thumb'))
                                            <img src="{{ $item['variant']->product->getFirstImageUrl('thumb') }}" alt="{{ $item['variant']->product->getDisplayNameAttribute() }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                            </div>
                                        @endif>
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['variant']->product->getDisplayNameAttribute() }}</p>
                                        <p class="text-xs text-gray-600">{{ $item['variant']->getVariantLabel() }} × {{ $item['quantity'] }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ number_format($item['subtotal'] / 100, 2) }} {{ config('store.currency', 'BDT') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <dl class="space-y-3 text-sm border-t border-gray-200 pt-4">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Subtotal') }}</dt>
                                <dd class="font-medium text-gray-900">{{ number_format($subtotal / 100, 2) }} {{ config('store.currency', 'BDT') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Shipping') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $shipping == 0 ? __('Free') : number_format($shipping / 100, 2) . ' ' . config('store.currency', 'BDT') }}</dd>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                                <dt>{{ __('Total') }}</dt>
                                <dd>{{ number_format($total / 100, 2) }} {{ config('store.currency', 'BDT') }}</dd>
                            </div>
                        </dl>

                        @if($subtotal < 200000)
                            <p class="mt-4 text-sm text-amber-600 text-center">{{ __('Add {{ amount }} more for free delivery!', ['amount' => number_format((200000 - $subtotal) / 100, 2)]) }}</p>
                        @else
                            <p class="mt-4 text-sm text-green-600 text-center">{{ __('You qualify for free delivery!') }}</p>
                        @endif>

                        <button type="submit" class="mt-6 w-full bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary/90 transition-colors">{{ __('Place Order') }}</button>

                        <div class="mt-4 flex items-center gap-2 text-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Secure checkout') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Cash on delivery') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ __('Easy returns') }}</span>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </div>

    @livewire('storefront.cart-drawer')
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const districtSelect = document.getElementById('district');
        const areaSelect = document.getElementById('area');

        if (districtSelect && areaSelect) {
            districtSelect.addEventListener('change', async () => {
                const districtId = districtSelect.value;
                areaSelect.innerHTML = '<option value="">{{ __('Select Area') }}</option>';
                areaSelect.disabled = true;

                if (!districtId) return;

                try {
                    const response = await fetch(`{{ route('api.districts.areas', ':districtId') }}`.replace(':districtId', districtId));
                    const data = await response.json();

                    data.forEach(area => {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = area.name;
                        areaSelect.appendChild(option);
                    });

                    areaSelect.disabled = false;
                } catch (e) {
                    console.error('Failed to load areas:', e);
                    areaSelect.disabled = false;
                }
            });
        }
    });
</script>
@endpush