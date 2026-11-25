@extends('layouts.app')

@section('title', $page->getDisplayNameAttribute() . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-primary">{{ __('Home') }}</a></li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li class="text-gray-900 font-medium" aria-current="page">{{ $page->getDisplayNameAttribute() }}</li>
            </ol>
        </nav>

        <article class="max-w-3xl mx-auto">
            <header class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $page->getDisplayNameAttribute() }}</h1>
            </header>

            <div class="prose prose-gray max-w-none">
                {!! $page->getDisplayContentAttribute() !!}
            </div>
        </article>
    </div>

    @livewireLazy('storefront.cart-drawer')
</div>
@endsection