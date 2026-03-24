@extends('layouts.app', ['title' => 'Editar cliente', 'heading' => 'Editar cliente', 'subheading' => 'Actualiza datos del cliente centralizado.'])

@section('content')
    <div class="panel"><form method="POST" action="{{ route('customers.update', $customer) }}">@csrf @method('PUT') @include('customers._form')</form></div>
@endsection
