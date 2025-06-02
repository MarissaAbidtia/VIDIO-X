@extends('layouts.app')

@section('title', isset($kategori) ? ucfirst($kategori) : 'Home')

@section('content')
<div class="container">
    <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="{{ asset('images/menu/' . $menu->gambar) }}" class="card-img-top" alt="{{ $menu->menu }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $menu->menu }}</h5>
                    <p class="card-text">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                    <a href="/beli/{{ $menu->idmenu }}" class="btn btn-primary">Beli</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
