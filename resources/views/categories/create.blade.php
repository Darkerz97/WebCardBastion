@extends('layouts.app', ['title' => 'Nueva categoria', 'heading' => 'Nueva categoria', 'subheading' => 'Organiza el catalogo publico y el panel administrativo.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('categories._form')
        </form>
    </div>
@endsection
