@if (! $isConfigured && $bkashEnabled)
    <x-filament::section>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-start gap-4" role="alert">
            <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-amber-800">
                    {{ __('bKash not configured') }}
                </h3>
                <p class="mt-1 text-sm text-amber-700">
                    {{ __('bKash is enabled but no valid Bangladesh mobile number is set. Customers cannot use bKash Send Money.') }}
                </p>
                <div class="mt-3">
                    <a href="{{ filament\admin\resources\settings-page }}" class="text-sm font-medium text-amber-800 hover:underline">
                        {{ __('Configure bKash number in Settings → Payments') }}
                    </a>
                </div>
            </div>
        </div>
    </x-filament::section>
@endif