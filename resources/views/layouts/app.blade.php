<?php $link = 'js/languages/'.$lang.'.js'; ?>
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('css/animate.css') }}" rel="stylesheet">
	<link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
	<link href="{{ asset('css/nprogress.css') }}" rel="stylesheet">
	{{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
<!-- JavaScripts -->
    <script>
        var HOME_URL = '{{url("/")}}';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="{{asset('js/nprogress.js')}}"></script>
    <script src="{{asset($link)}}"></script>
    <script src="{{asset('js/core.js')}}"></script>

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/game') }}">
                    BitColor
                </a>

            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                {{--<ul class="nav navbar-nav">
                    <li><a href="{{ url('/home') }}">Home</a></li>
                </ul>--}}

                <ul class="nav navbar-nav">
                    <li><a  href="{{ url('/game') }}">{{translate('game')}}</a></li>
                    <li><a  href="{{ url('/statistic') }}">{{translate('statistic')}}</a></li>

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">{{translate('login')}}</a></li>
                        <li><a href="{{ url('/register') }}">{{translate('register')}}</a></li>
                    @else
                        <li id="my_balance">{{$balance}} <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="fa fa-bell-o" aria-hidden="true"></i><span class="badge notif_count"@if($countNotif==0) style="display:none" @endif>{{$countNotif}}</span>
                            </a>

                            <ul class="dropdown-menu notif_top" role="menu">
                                <?php if (count($listNotif)>0){?>
                                    @foreach ($listNotif as $k=>$v)
                                    <li @if($v->seen==0) class="unseen" onmouseover="notificationSeen({{$v->notificationId}})" @endif><a href="@if($v->modal==1){{$v->link}}@else#@endif">{{translate($v->titleKey)}}</a></li>
                                    @endforeach
                                <?php }?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/settings') }}"><i class="fa fa-btn fa-cogs"></i>{{translate('settings')}}</a></li>
                                <li><a href="{{ url('/logout') }}" data-toggle="logout"><i class="fa fa-btn fa-sign-out"></i>{{translate('logout')}}</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>