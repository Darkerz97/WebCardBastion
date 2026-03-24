@extends('layouts.app', ['title' => 'Editar producto', 'heading' => 'Editar producto', 'subheading' => 'Actualiza stock, catalogo publico e imagenes del producto.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('products._form')
        </form>
    </div>
@endsection
