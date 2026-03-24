@extends('layouts.app', ['title' => 'Nuevo producto', 'heading' => 'Nuevo producto', 'subheading' => 'Alta manual de catálogo central.'])

@section('content')
    <div class="panel"><form method="POST" action="{{ route('products.store') }}">@csrf @include('products._form')</form></div>
@endsection
