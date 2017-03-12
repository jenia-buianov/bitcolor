@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div id="mainContent" class="col-md-8 col-lg-8">
                @include('game.content')
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="bs-component">
                    <div class="list-group">
                        <a href="{{url('/game')}}" class="list-group-item @if(Request::is('game')) active @endif">
                            <span class="badge" id="currGames">{{$currGames}}</span>
                            {{translate('games')}}
                        </a>
                        <a href="{{url('/game/active')}}" class="list-group-item @if(Request::is('game/active')) active @endif">
                            <span class="badge" id="myActive">{{$myActive}}</span>
                            {{translate('my_active')}}
                        </a>
                        <a href="{{url('/statistic')}}" class="list-group-item @if(Request::is('statistic')) active @endif">
                            <span class="badge" id="myStatistic">{{$myStatistic}}</span>
                            {{translate('success')}}
                        </a>
                        <a href="{{url('/statistic/history')}}" class="list-group-item @if(Request::is('statistic/history')) active @endif">
                            <span class="badge" id="myPlayedGames">{{$myPlayedGames}}</span>
                            {{translate('played')}}
                        </a>
                        <a href="{{url('/statistic/top')}}" class="list-group-item @if(Request::is('statistic/top')) active @endif">
                            <span class="badge" id="placeTop">{{$top}}</span>
                            {{translate('top')}}
                        </a>
                    </div>
                </div>

            </div>
    </div>
@endsection