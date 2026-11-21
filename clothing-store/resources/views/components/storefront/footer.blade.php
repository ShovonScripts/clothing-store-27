<footer class="bg-gray-50 border-t border-gray-100 mt-auto">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4" aria-label="{{ config('store.name', '{BRAND}') }} - Home">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-primary">{{ config('store.name', '{BRAND}') }}</span>
                </a>
                <p class="text-sm text-gray-600 mb-4">{{ __('Men\'s fashion for the modern Bangladeshi gentleman. Quality clothing, fair prices, delivered to your door.') }}</p>
                <div class="flex gap-4">
                    @if(settings('facebook_url'))
                        <a href="{{ settings('facebook_url') }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                    @endif
                    @if(settings('instagram_url'))
                        <a href="{{ settings('instagram_url') }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif
                    @if(settings('tiktok_url'))
                        <a href="{{ settings('tiktok_url') }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-primary transition-colors" aria-label="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.548.621a11.83 11.83 0 0 0-2.502.31C8.413 1.367 7.023 3.057 6.603 4.923a11.436 11.436 0 0 0-1.017 3.272c-.182 1.418.04 2.837.574 4.087a13.158 13.158 0 0 0 1.647 3.736c.39.531.853 1.013 1.338 1.404.487.389.987.752 1.5.99.298.14.605.19.89.19.41 0 .81-.11 1.195-.258.733-.27 1.425-.68 2.045-1.13.414-.318.813-.67 1.19-1.042.382-.384.725-.795 1.018-1.21.425-.606.773-1.237 1.044-1.886.342-.827.544-1.68.602-2.548.058-.887.086-1.778.086-2.676 0-2.373-.34-4.665-1.016-6.804a7.442 7.442 0 0 0-2.797-4.12c-.838-.782-1.843-1.39-2.85-1.647-.41-.098-.838-.147-1.254-.147-.435 0-.873.053-1.275.155a7.44 7.44 0 0 0-3.79 1.13 9.152 9.152 0 0 0-1.33 3.52c-.146.885-.09 1.774.162 2.627.22.767.635 1.505 1.152 2.22.32.448.704.845 1.127 1.18.406.326.852.59 1.3.73.68.21 1.395.256 2.076.11.607-.127 1.168-.397 1.658-.797.73-.59 1.328-1.342 1.787-2.076.27-.438.473-.878.574-1.338a13.148 13.148 0 0 0-.574-4.087 11.413 11.413 0 0 0-3.272-1.017c-1.866-.42-3.556-2.11-4.924-4.302a11.82 11.82 0 0 0-.31-2.502c-.17-.822-.366-1.64-.621-2.449-.256-.81-.556-1.62-.899-2.418a11.798 11.798 0 0 1-1.018-3.639c-.188-1.017-.282-2.052-.282-3.102 0-2.403.37-4.762 1.105-7.03.736-2.268 1.923-4.415 3.537-6.115 1.613-1.7 3.614-3.032 5.845-3.763 2.23-.73 4.597-1.09 6.975-1.09 2.44 0 4.807.394 7.05 1.19 2.243.795 4.336 2.127 6.115 3.998z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('shop') }}" class="hover:text-primary transition-colors">{{ __('Shop All') }}</a></li>
                    <li><a href="{{ route('categories') }}" class="hover:text-primary transition-colors">{{ __('Categories') }}</a></li>
                    <li><a href="{{ route('pages.show', 'about') }}" class="hover:text-primary transition-colors">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('pages.show', 'contact') }}" class="hover:text-primary transition-colors">{{ __('Contact') }}</a></li>
                    <li><a href="{{ route('track-order') }}" class="hover:text-primary transition-colors">{{ __('Track Order') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Support') }}</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('pages.show', 'delivery-info') }}" class="hover:text-primary transition-colors">{{ __('Delivery Information') }}</a></li>
                    <li><a href="{{ route('pages.show', 'return-exchange') }}" class="hover:text-primary transition-colors">{{ __('Returns & Exchanges') }}</a></li>
                    <li><a href="{{ route('pages.show', 'size-guide') }}" class="hover:text-primary transition-colors">{{ __('Size Guide') }}</a></li>
                    <li><a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-primary transition-colors">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="{{ route('pages.show', 'terms') }}" class="hover:text-primary transition-colors">{{ __('Terms of Service') }}</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Contact Us') }}</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    @if(settings('store_phone'))
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 2.257a1 1 0 00-.36.694l-.071.746a1 1 0 01-1.437 1.437l-.746-.071a1 1 0 00-.694.36L5.17 16.071a1 1 0 01-1.21.502L3.684 14.574a1 1 0 01-.684-.948V7.72a2 2 0 012-2H15a2 2 0 012 2v3.28a1 1 0 01-.684.948l-1.498 4.493a1 1 0 01-1.21.502l-2.257-2.257a1 1 0 00-.694-.36l-.746.071a1 1 0 01-1.437-1.437l.071-.746a1 1 0 00.36-.694l2.257-2.257a1 1 0 01.502-1.21L17.32 5.17a1 1 0 01.948-.684H21a2 2 0 012 2v3.28a1 1 0 01-.684.948l-1.498 4.493a1 1 0 01-1.21.502L13.58 14.43a1 1 0 00-.694.36l-.071.746a1 1 0 01-1.437 1.437l-.746-.071a1 1 0 00-.694-.36L9.83 9.17a1 1 0 01-.502-1.21L8.33 6.67a1 1 0 01.684-.948H11a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 2.257a1 1 0 00-.36.694l-.071.746a1 1 0 01-1.437 1.437l-.746-.071a1 1 0 00-.694.36L5.17 16.071a1 1 0 01-1.21.502L3.684 14.574a1 1 0 01-.684-.948V7.72a2 2 0 012-2z" /></svg>
                            <a href="tel:{{ settings('store_phone') }}" class="hover:text-primary transition-colors">{{ settings('store_phone') }}</a>
                        </li>
                    @endif
                    @if(settings('store_address'))
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>{{ settings('store_address') }}</span>
                        </li>
                    @endif
                    @if(settings('whatsapp_number'))
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.454.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.471.099-.174.05-.372-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.42.01-.643.078-.223.068-.482.14-.74.213-.257.074-.55.16-.825.273-.274.113-.559.22-.84.324-.282.103-.559.189-.842.273-.283.085-.558.182-.837.249-.278.068-.553.1-.813.114-.274.005-.573-.062-.846-.2-.273-.14-.547-.382-.826-.655-.278-.274-.527-.598-.713-.845-.188-.25-.382-.51-.574-.669-.19-.15-.402-.273-.603-.373-.198-.098-.413-.173-.618-.223zM12 2C6.477 2 2 6.477 2 12c0 1.825.425 3.537 1.168 5.084L2 22l5.452-1.248A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                            <a href="https://wa.me/{{ settings('whatsapp_number') }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">{{ __('WhatsApp') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500 text-center md:text-left">
                    &copy; {{ date('Y') }} {{ config('store.name', '{BRAND}') }}. {{ __('All rights reserved.') }}
                </p>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-primary transition-colors">{{ __('Privacy Policy') }}</a>
                    <a href="{{ route('pages.show', 'terms') }}" class="hover:text-primary transition-colors">{{ __('Terms of Service') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>