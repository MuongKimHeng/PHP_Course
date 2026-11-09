<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Larabook - {{ $title??'Home' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <nav class="nav nav-pills nav-justified mb-4">
        <a class="nav-link {{ request()->routeIs('topics.*') ? 'active' : '' }}"
           href="{{ route('topics.index') }}">Main Page</a>
        <a class="nav-link {{ request()->routeIs('blocks.*') ? 'active' : '' }}"
           href="{{ route('blocks.create') }}">Content Control</a>
    </nav>

    <div class="container">
        {{-- Render section content --}}
        {{-- @yield('content')   --}}
        @section('content')
            <h1>Larabook Master</h1>        
        @show
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    @yield('scripts')
</body>
</html>