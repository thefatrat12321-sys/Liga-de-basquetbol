@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>🏀 Iniciar Sesión</h2>
            <p class="subtitle">Accede al panel de gestión de la liga.</p>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                    <label for="remember">Recordarme</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>

            <div class="auth-footer">
                ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
            </div>

            <div class="auth-hint">
                <strong>Cuentas demo</strong><br>
                admin@liga.com / admin123 (admin)<br>
                arbitro@liga.com / arbitro123 (árbitro)<br>
                espectador@liga.com / usuario123 (solo lectura)
            </div>
        </div>
    </div>
@endsection
