@extends('layouts.app')

@section('title', 'Editar Equipo')

@section('content')
    <div class="page-header">
        <h1 class="page-title">✏️ Editar Equipo</h1>
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">&larr; Volver a Equipos</a>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('equipos.update', $equipo) }}">
            @csrf
            @method('PUT')

            @include('equipos._form', ['equipo' => $equipo])
        </form>
    </div>
@endsection
