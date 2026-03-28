@extends('layouts.app', ['title' => 'Nueva preventa', 'heading' => 'Nueva preventa', 'subheading' => 'Registra una preventa, asignala a un cliente y deja listo su seguimiento de abonos.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('preorders.store') }}">
            @csrf
            @include('preorders._form')
        </form>
    </div>
@endsection
