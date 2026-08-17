<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Management</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <div class="container">
        <header>
            <h1>Leave Management</h1>
            @auth
            <div>Welcome, {{ Auth::user()->first_name }} | <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            @endauth
        </header>

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
