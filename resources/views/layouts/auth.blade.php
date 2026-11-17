<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'El Pirry')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
<style>
    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #ff8c42 0%, #e6702a 100%);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }
    .auth-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,.2);
        overflow: hidden;
        max-width: 900px;
        width: 100%;
        margin: 24px;
    }
    .auth-side {
        background: linear-gradient(160deg, #ff8c42, #e6702a);
        color: #fff;
        padding: 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }
    .auth-side h1 { font-weight: 700; }
    .btn-pirry {
        background: #ff8c42;
        border-color: #ff8c42;
        color: #fff;
        font-weight: 600;
    }
    .btn-pirry:hover { background: #e6702a; border-color: #e6702a; color: #fff; }
</style>
@stack('styles')
</head>
<body>

<div class="auth-card">
    <div class="row g-0">
        <div class="col-lg-5 d-none d-lg-block">
            <div class="auth-side">
                @yield('side_title', 'Bienvenido')
                @yield('side_text')
            </div>
        </div>
        <div class="col-lg-7 p-4 p-md-5">
            @yield('content')
        </div>
    </div>
</div>

<script>
window.flashMessages = window.flashMessages || [];
@if(session('success'))
  window.flashMessages.push({ type: 'success', message: @json(session('success')) });
@endif
@if(session('error'))
  window.flashMessages.push({ type: 'error', message: @json(session('error')) });
@endif
</script>
<script src="{{ asset('js/toast.js') }}"></script>
</body>
</html>
