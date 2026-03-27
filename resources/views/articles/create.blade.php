@extends('layouts.app', ['title' => 'Nueva entrada', 'heading' => 'Nueva entrada', 'subheading' => 'Publica una nueva pieza editorial para la comunidad Card Bastion.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data">
            @csrf
            @include('articles._form')
        </form>
    </div>
@endsection
