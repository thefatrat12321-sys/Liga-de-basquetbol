@extends('layouts.app')

@section('title', 'Nuevo Jugador')

@section('content')
    <div class="page-header">
        <h1 class="page-title">👤 Registrar Nuevo Jugador</h1>
        <a href="{{ route('jugadores.index') }}" class="btn btn-secondary">&larr; Volver a Jugadores</a>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('jugadores.store') }}">
            @csrf

            @include('jugadores._form', ['jugador' => null, 'equipoSeleccionado' => $equipoSeleccionado])
        </form>
    </div>
@endsection
