@extends('layouts.app')

@section('title', 'Makanan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Daftar Makanan</h2>
        <div class="row">
            @foreach($menus as $menu)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="{{ $menu->gambar }}" class="card-img-top" alt="{{ $menu->nama }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $menu->nama }}</h5>
                        <p class="card-text">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                        <a href="#" class="btn btn-primary">Pesan</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>