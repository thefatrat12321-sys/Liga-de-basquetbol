@extends('layouts.app')

@section('title', 'Editar Jugador')

@section('content')
    <div class="page-header">
        <h1 class="page-title">✏️ Editar Jugador</h1>
        <a href="{{ route('jugadores.index') }}" class="btn btn-secondary">&larr; Volver a Jugadores</a>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('jugadores.update', $jugador) }}">
            @csrf
            @method('PUT')

            @include('jugadores._form', ['jugador' => $jugador])
        </form>
    </div>
@endsection
