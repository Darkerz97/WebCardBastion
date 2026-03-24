@extends('layouts.app', ['title' => $product->name, 'heading' => $product->name, 'subheading' => 'Detalle de producto en catálogo central.'])

@section('content')
    <div class="panel card-stack">
        <div class="grid grid-2">
            <div><strong>SKU:</strong> {{ $product->sku }}</div>
            <div><strong>Barcode:</strong> {{ $product->barcode ?: 'N/D' }}</div>
            <div><strong>Categoría:</strong> {{ $product->category ?: 'N/D' }}</div>
            <div><strong>Estado:</strong> {{ $product->active ? 'Activo' : 'Inactivo' }}</div>
            <div><strong>Costo:</strong> ${{ number_format($product->cost, 2) }}</div>
            <div><strong>Precio:</strong> ${{ number_format($product->price, 2) }}</div>
            <div><strong>Stock:</strong> {{ $product->stock }}</div>
            <div><strong>UUID:</strong> <span class="muted">{{ $product->uuid }}</span></div>
        </div>
        <div><strong>Descripción</strong><p class="muted">{{ $product->description ?: 'Sin descripción.' }}</p></div>
    </div>
@endsection
