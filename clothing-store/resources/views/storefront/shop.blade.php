@extends('layouts.app')

@section('title', __('Shop') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    @livewire('storefront.shop', ['categorySlug' => $categorySlug ?? ''], key('shop'))
    @livewireLazy('storefront.cart-drawer')
</div>
@endsection