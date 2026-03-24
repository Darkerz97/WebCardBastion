@extends('layouts.app', ['title' => 'Nuevo producto', 'heading' => 'Nuevo producto', 'subheading' => 'Alta de producto para inventario interno y tienda virtual.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('products._form')
        </form>
    </div>
@endsection
