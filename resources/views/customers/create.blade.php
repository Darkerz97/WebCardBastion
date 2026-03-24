@extends('layouts.app', ['title' => 'Nuevo cliente', 'heading' => 'Nuevo cliente', 'subheading' => 'Alta de cliente para historial y sincronización.'])

@section('content')
    <div class="panel"><form method="POST" action="{{ route('customers.store') }}">@csrf @include('customers._form')</form></div>
@endsection
