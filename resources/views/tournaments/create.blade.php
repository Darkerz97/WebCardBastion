@extends('layouts.app', ['title' => 'Nuevo torneo', 'heading' => 'Nuevo torneo', 'subheading' => 'Configura un nuevo evento competitivo para la comunidad.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('tournaments.store') }}">
            @csrf
            @include('tournaments._form')
        </form>
    </div>
@endsection
