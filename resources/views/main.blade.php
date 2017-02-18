<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}"/>

    </head>
    <body>
        <div class="container">
            @yield('select')
            @yield('owner_reg')
            <div class="content">

                @yield('content')
            </div>
        </div>
    </body>
</html>
