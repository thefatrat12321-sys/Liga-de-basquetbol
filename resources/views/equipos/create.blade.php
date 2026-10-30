@extends('layouts.app')

@section('title', 'Nuevo Equipo')

@section('content')
    <div class="page-header">
        <h1 class="page-title">🛡️ Registrar Nuevo Equipo</h1>
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">&larr; Volver a Equipos</a>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('equipos.store') }}">
            @csrf

            @include('equipos._form')
        </form>
    </div>
@endsection
