<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Install Pingu</title>

    <!-- Scripts -->
    {!! Asset::container('installer')->scripts() !!}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    {!! Asset::container('installer')->styles() !!}
</head>

<body>
    <main class="container">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-6">
                @yield('body')
            </div>
        </div>
    </main>
</body>

</html>
