@extends('layouts.app')

@section('title', __('Shop') . ' - ' . config('store.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    @livewire('storefront.shop', ['categorySlug' => $categorySlug ?? ''], key('shop'))
    @livewire('storefront.cart-drawer')
</div>
@endsection