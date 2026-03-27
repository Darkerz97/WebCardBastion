@extends('layouts.app', ['title' => 'Editar entrada', 'heading' => 'Editar entrada', 'subheading' => 'Ajusta contenido, portada y configuracion de comentarios.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('articles.update', $article) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('articles._form')
        </form>
    </div>
@endsection
