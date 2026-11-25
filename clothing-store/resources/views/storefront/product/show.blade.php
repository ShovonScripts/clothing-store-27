@extends('layouts.app')

@section('title', $product->getDisplayNameAttribute() . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">

    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->getDisplayNameAttribute(),
            'description' => $product->short_description ?? strip_tags($product->description ?? ''),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => config('store.name', '{BRAND}'),
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => config('store.currency', 'BDT'),
                'price' => number_format($product->price / 100, 2, '.', ''),
                'availability' => $product->variants->where('is_active', true)->where('stock', '>', 0)->isNotEmpty() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => config('store.name', '{BRAND}'),
                ],
            ],
        ];

        if ($product->getFirstImageUrl('large')) {
            $jsonLd['image'] = $product->getFirstImageUrl('large');
        }

        if ($product->compare_at_price && $product->price < $product->compare_at_price) {
            $jsonLd['offers']['priceValidUntil'] = now()->addMonth()->format('Y-m-d');
        }
    @endphp

    <script type="application/ld+json">
        {{ json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
    </script>

    <div class="container mx-auto px-4 py-8">
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li><a href="{{ route('shop') }}" class="hover:text-primary">{{ __('Shop') }}</a></li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                @if($product->category)
                    <li><a href="{{ route('shop', ['category' => $product->category->slug]) }}" class="hover:text-primary">{{ $product->category->getDisplayNameAttribute() }}</a></li>
                    <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                @endif
                <li class="text-gray-900 font-medium truncate max-w-xs" aria-current="page">{{ $product->getDisplayNameAttribute() }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="lg:sticky lg:top-24 lg:self-start">
                @livewire('storefront.product-gallery', ['product' => $product], key('product-gallery-' . $product->id))
            </div>

            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ $product->category?->getDisplayNameAttribute() }}</p>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->getDisplayNameAttribute() }}</h1>

                <div class="flex items-center gap-4 mb-4">
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($product->price / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                    @if($product->compare_at_price && $product->price < $product->compare_at_price)
                        <span class="text-xl text-gray-500 line-through">{{ number_format($product->compare_at_price / 100, 2) }} {{ config('store.currency', 'BDT') }}</span>
                        <span class="bg-red-50 text-red-600 text-sm font-medium px-2 py-1 rounded">
                            -{{ round(($product->compare_at_price - $product->price) / $product->compare_at_price * 100) }}%
                        </span>
                    @endif
                </div>

                @if($product->short_description)
                    <div class="text-gray-600 mb-6">{{ $product->short_description }}</div>
                @endif

                @livewire('storefront.variant-picker', ['product' => $product], key('variant-picker-' . $product->id))

                <div class="border-t border-gray-200 pt-6 mt-6 space-y-4">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Delivery Information') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Order by 2 PM for same-day dispatch in Dhaka. 2-3 days nationwide.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Easy Returns') }}</p>
                            <p class="text-sm text-gray-600">{{ __('7-day hassle-free returns and exchanges. Items must be unworn with tags.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('Cash on Delivery') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Pay when you receive your order. Available everywhere in Bangladesh.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="https://wa.me/{{ settings('whatsapp_number') }}?text={{ urlencode(__('Hi, I have a question about ') . $product->getDisplayNameAttribute()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.454.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.471.099-.174.05-.372-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.42.01-.643.078-.223.068-.482.14-.74.213-.257.074-.55.16-.825.273-.274.113-.559.22-.84.324-.282.103-.559.189-.842.273-.283.085-.558.182-.837.249-.278.068-.553.1-.813.114-.274.005-.573-.062-.846-.2-.273-.14-.547-.382-.826-.655-.278-.274-.527-.598-.713-.845-.188-.25-.382-.51-.574-.669-.19-.15-.402-.273-.603-.373-.198-.098-.413-.173-.618-.223zM12 2C6.477 2 2 6.477 2 12c0 1.825.425 3.537 1.168 5.084L2 22l5.452-1.248A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                        {{ __('Ask a Question') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div class="border-b border-gray-200 mb-8">
                <nav class="flex gap-8" aria-label="Product tabs">
                    <button type="button" class="pb-4 border-b-2 border-primary text-primary font-medium" aria-selected="true">{{ __('Description') }}</button>
                    <button type="button" class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium" aria-selected="false">{{ __('Size Guide') }}</button>
                    <button type="button" class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium" aria-selected="false">{{ __('Care Instructions') }}</button>
                </nav>
            </div>

            <div class="prose prose-gray max-w-none">
                @if($product->description)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Description') }}</h2>
                        <div>{!! $product->description !!}</div>
                    </div>
                @endif

                @if($product->fabric)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Fabric & Material') }}</h2>
                        <div>{!! $product->fabric !!}</div>
                    </div>
                @endif

                @if($product->fit)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Fit') }}</h2>
                        <div>{!! $product->fit !!}</div>
                    </div>
                @endif

                @if($product->care)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Care Instructions') }}</h2>
                        <div>{!! $product->care !!}</div>
                    </div>
                @endif

                @if($product->size_chart)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('Size Chart') }}</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach($product->size_chart->headers as $header)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->size_chart->rows as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($relatedProducts->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('You May Also Like') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        @include('storefront.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @livewireLazy('storefront.cart-drawer')
</div>
@endsection

@push('scripts')
@php
    $metaPixel = app(\App\Services\MetaPixelService::class);
    $eventId = \Illuminate\Support\Str::uuid()->toString();
    View::share('metaPixelEventId', $eventId);
@endphp
@if($metaPixel->isEnabled())
    {!! $metaPixel->getViewContentScript([
        'id' => $product->id,
        'name' => $product->getDisplayNameAttribute(),
        'category' => $product->category?->getDisplayNameAttribute() ?? '',
        'price' => $product->price,
    ], $eventId) !!}
@endif
@endpush
