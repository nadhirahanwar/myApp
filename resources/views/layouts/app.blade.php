<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel App')</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .navbar-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">My App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            @auth
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown d-flex align-items-center">

                        @php
                            $avatarPath = Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar)
                                ? Storage::url(Auth::user()->avatar)
                                : asset('images/default-avatar.png');
                        @endphp

                        <img src="{{ $avatarPath }}" alt="Avatar" class="navbar-avatar">

                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->nickname ?? Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </li>
                </ul>
            @else
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@stack('scripts')
</body>
</html>
