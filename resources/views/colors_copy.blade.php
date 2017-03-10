@extends('layouts.app')
@section('content')


    <!-- Fonts -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">--}}
    {{--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">--}}
	{{--<link href="{{ asset('css/style.css') }}" rel="stylesheet">--}}
    


    <!--[if lt IE 8]>
    <!--<link rel="stylesheet" href="http://gnp/css/blueprint/ie.css" type="text/css" media="screen, projection">-->
    <![endif]-->

    <!--[if IE 6]>
    <!--<link rel="stylesheet" href="http://gnp/css/ie6.css" type="text/css">-->
    <![endif]-->


    {{--<script type='text/javascript' src='{your_path_to}/jQuery.radmenu.js'></script>--}}


<div class="box" class="col-xs-12">

    <div class="col-xs-4"  style="">
     {{--border:1px solid #0080FF;height:700px;--}}
        <form action="{{ url('game')}}" method="POST" class="form-horizontal">

            {{ csrf_field() }}
            @include('errors.validation')
            <div class="col-xs-8">
              <label for="amount"> email </label>
                <input type="text"  name="email" class="form-control" value="{{$email}}"
                readonly >
            </div>
            <div class="col-xs-8">
                <label for="amount"> amount </label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}">
            </div>
            <div class="col-xs-8">
                <label for="sector"> sector </label>
                <select name="sector" class="form-control" value="{{ old('sector') }}"
                        onmousedown="$(':first-child', this).remove(); this.onmousedown = null;">
                    <option></option>
                    <option value="red">red</option>
                    <option value="orange">orange</option>
                    <option value="yellow">yellow</option>
                    <option value="green">green</option>
                    <option value="cyan">cyan</option>
                    <option value="blue">blue</option>
                    <option value="violet">violet</option>
                    <option value="lucky">lucky</option>
                </select>
            </div>

            <div class="form-group">
                <div class="col-xs-8 col-xs-offset-2">
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-btn fa-plus"></i>Bet
                    </button>
                </div>
            </div>
        </form>
        <div id="bets">
            <table>
                <tr>
                    <th> email:</th>
                    <th> Bet:</th>
                    <th> sector:</th>
                </tr>

                    @if (isset($bets))
                    @foreach($bets as $bet)
                        <tr>
                            <td>{{$bet->email}}</td>
                            <td>{{$bet->amount}}</td>
                            <td>{{$bet->sector}}</td>
                        </tr>
                    @endforeach
                    @endif

            </table>
        </div>
    </div>

        <div id="container" class="col-xs-4">
            <button class="color" id="red"></button>
            <button class="color" id="orange"></button>
            <button class="color" id="yellow"></button>
            <button class="color" id="green"></button>
            <button class="color" id="blue"></button>
            <button class="color" id="azure"></button>
            <button class="color" id="purple"></button>
            <button class="color" id="custom"></button>
        </div>

</div>


@endsection