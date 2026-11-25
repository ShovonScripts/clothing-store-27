<div class="min-h-screen bg-gray-50">
    @livewireLazy('storefront.cart-drawer')

    <main class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('global.checkout') }}</h1>
            <p class="text-gray-500 mt-1">{{ __('Enter your details to complete your order') }}</p>
        </div>

        @if ($error)
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700" role="alert">
                {{ $error }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Contact Information') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="form.name" id="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required>
                            @error('form.name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                            <input type="tel" wire:model.defer="form.phone" id="phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required placeholder="01XXXXXXXXX">
                            @error('form.phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email (optional)') }}</label>
                            <input type="email" wire:model.defer="form.email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            @error('form.email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Delivery Address') }} <span class="text-red-500">*</span></h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('District') }} <span class="text-red-500">*</span></label>
                            <select wire:model="form.district_id" id="district_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required>
                                <option value="">{{ __('Select district') }}</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district['id'] }}">{{ app()->getLocale() === 'bn' ? $district['name_bn'] : $district['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.district_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="area_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Area') }} <span class="text-red-500">*</span></label>
                            <select wire:model="form.area_id" id="area_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required>
                                <option value="">{{ __('Select area') }}</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area['id'] }}">{{ app()->getLocale() === 'bn' ? $area['name_bn'] : $area['name'] }}</option>
                                @endforeach
                            </select>
                            @error('form.area_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Detailed Address') }} <span class="text-red-500">*</span></label>
                        <textarea wire:model.defer="form.address" id="address" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" required placeholder="House/Road/Block/Post office..."></textarea>
                        @error('form.address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Honeypot (hidden from humans) -->
                <div class="hidden" aria-hidden="true">
                    <label for="website_url">Website</label>
                    <input type="text" wire:model="honeypot" id="website_url" tabindex="-1" autocomplete="off">
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Payment Method') }} <span class="text-red-500">*</span></h2>

                    @php
                        $bkashAvailable = $this->bkashAvailable;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ $form['payment_method'] === 'cod' ? 'border-primary bg-primary/5' : 'border-gray-200' }}">
                            <input type="radio" wire:model="form.payment_method" value="cod" class="sr-only peer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H6a2 2 0 00-2 2v4m8 0a2 2 0 012 2v4m0 0H6m10 0l-2 2h-8a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v4m0 0l2 2" /></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ __('Cash on Delivery') }}</span>
                                    <p class="text-xs text-gray-500">{{ __('Pay when you receive your order') }}</p>
                                </div>
                            </div>
                        </label>

                        @if ($bkashAvailable)
                            <label class="relative flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors {{ $form['payment_method'] === 'bkash_send_money' ? 'border-primary bg-primary/5' : 'border-gray-200' }}">
                                <input type="radio" wire:model="form.payment_method" value="bkash_send_money" class="sr-only peer">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ __('bKash Send Money') }}</span>
                                        <p class="text-xs text-gray-500">{{ __('Pay via bKash, then enter TrxID') }}</p>
                                    </div>
                                </div>
                            </label>
                        @else
                            <div class="relative flex items-center p-4 border-2 rounded-xl bg-gray-50 border-gray-200 cursor-not-allowed opacity-70">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">{{ __('bKash Send Money') }}</span>
                                        <p class="text-xs text-gray-400">{{ __('Not configured') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- bKash Fields -->
                @if ($showBkashFields)
                    <div class="bg-pink-50 rounded-xl border border-pink-100 p-6" wire:transition>
                        <h3 class="text-lg font-medium text-pink-800 mb-4">{{ __('bKash Payment Details') }}</h3>
                        <p class="text-sm text-pink-700 mb-4">{{ __('Send money to the number shown, then enter the TrxID and sender number below.') }}</p>
                        <p class="text-sm font-semibold text-pink-800 mb-4">
                            {{ __('Store bKash number') }}: <span class="font-mono">{{ settings('bkash_number') }}</span>
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="bkash_trxid" class="block text-sm font-medium text-gray-700 mb-1">{{ __('TrxID') }} <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="form.bkash_trxid" id="bkash_trxid" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="C12AB34D" required>
                                @error('form.bkash_trxid') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="bkash_sender" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sender Number') }} <span class="text-red-500">*</span></label>
                                <input type="tel" wire:model.defer="form.bkash_sender" id="bkash_sender" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="01XXXXXXXXX" required>
                                @error('form.bkash_sender') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="bkash_amount" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Amount (BDT)') }} <span class="text-red-500">*</span></label>
                                <input type="number" wire:model.defer="form.bkash_amount" id="bkash_amount" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="{{ $pricing['total'] ?? 0 }}" required>
                                @error('form.bkash_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Coupon -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Coupon Code (Optional)') }}</h2>

                    @if ($pricing && $pricing['coupon'])
                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div>
                                <p class="font-medium text-green-800">{{ $pricing['coupon']['name'] }}</p>
                                <p class="text-sm text-green-600">Discount: {{ moneyShort($pricing['coupon']['discount']) }}</p>
                            </div>
                            <button wire:click="removeCoupon" class="text-sm text-red-600 hover:text-red-800">{{ __('Remove') }}</button>
                        </div>
                    @else
                        <div class="flex gap-2">
                            <input type="text" wire:model.defer="form.coupon_code" placeholder="Enter coupon code" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                            <button wire:click="applyCoupon" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">{{ __('Apply') }}</button>
                        </div>
                        @error('form.coupon_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    @endif
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Notes (Optional)') }}</h2>
                    <textarea wire:model.defer="form.note" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Any special instructions for delivery..."></textarea>
                </div>

                <!-- Submit -->
                <div class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24">
                    <button wire:click="submit" :disabled="processing" class="w-full py-3 px-6 bg-primary text-white rounded-lg font-medium text-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        @if ($processing)
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ __('Processing...') }}
                        @else
                            {{ __('Place Order') }}
                        @endif
                    </button>

                    <p class="mt-3 text-center text-xs text-gray-500">{{ __('By placing this order, you agree to our') }} <a href="{{ route('pages.show', 'terms') }}" class="text-primary hover:underline">{{ __('Terms of Service') }}</a> {{ __('and') }} <a href="{{ route('pages.show', 'privacy-policy') }}" class="text-primary hover:underline">{{ __('Privacy Policy') }}</a>.</p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Order Summary') }} ({{ $cartCount }})</h2>

                    @if ($items->isEmpty())
                        <p class="text-gray-500 text-center py-8">{{ __('Your cart is empty') }}</p>
                    @else
                        <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                            @foreach ($items as $item)
                                <div class="flex gap-3">
                                    <img src="{{ $item['image'] ?? asset('images/placeholder.svg') }}" alt="{{ $item['product_name'] }}" class="w-12 h-12 object-cover rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['product_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['variant_size'] }} / {{ $item['variant_color'] }} × {{ $item['qty'] }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ moneyShort($item['price'] * $item['qty']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($pricing)
                            <dl class="space-y-2 text-sm border-t pt-4">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('Subtotal') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ moneyShort($pricing['subtotal']) }}</dd>
                                </div>

                                @if ($pricing['discount'] > 0)
                                    <div class="flex justify-between text-green-600">
                                        <dt>{{ __('Discount') }}</dt>
                                        <dd class="font-medium">-{{ moneyShort($pricing['discount']) }}</dd>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">{{ __('Delivery') }}</dt>
                                    <dd class="font-medium text-gray-900">{{ moneyShort($pricing['delivery_charge']) }}</dd>
                                </div>

                                <div class="border-t pt-2 flex justify-between">
                                    <dt class="font-semibold text-gray-900">{{ __('Total') }}</dt>
                                    <dd class="font-bold text-lg text-gray-900">{{ moneyShort($pricing['total']) }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="text-gray-500 text-center py-4">{{ __('Enter address to calculate total') }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>