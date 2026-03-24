@extends('layouts.app', ['title' => 'Editar producto', 'heading' => 'Editar producto', 'subheading' => 'Ajusta catálogo, precios y stock.'])

@section('content')
    <div class="panel"><form method="POST" action="{{ route('products.update', $product) }}">@csrf @method('PUT') @include('products._form')</form></div>
@endsection
