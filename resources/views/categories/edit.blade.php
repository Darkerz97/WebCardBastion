@extends('layouts.app', ['title' => 'Editar categoria', 'heading' => 'Editar categoria', 'subheading' => 'Actualiza nombre, orden y visual de la categoria.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('categories._form')
        </form>
    </div>
@endsection
