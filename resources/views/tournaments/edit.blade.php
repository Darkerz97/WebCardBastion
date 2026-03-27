@extends('layouts.app', ['title' => 'Editar torneo', 'heading' => 'Editar torneo', 'subheading' => 'Actualiza estado, fechas y configuracion del evento.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('tournaments.update', $tournament) }}">
            @csrf
            @method('PUT')
            @include('tournaments._form')
        </form>
    </div>
@endsection
